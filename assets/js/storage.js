require('../css/storage.scss');

import modal from "./components/modal";
import spreadsheet from "./components/spreadsheet";
import update from "./components/update";
import uploader from "./components/uploader";

(function () {
    'use strict';

    /**
     * Upload progress constants.
     * @type {number}
     */
    const
        PROGRESS_WAITING = 0,
        PROGRESS_UPLOADING = 1,
        PROGRESS_MOVING = 2,
        PROGRESS_DONE = 3;

    /**
     * HTTP Response status codes.
     * @type {number}
     */
    const
        RESPONSE_OK = 200,
        RESPONSE_PARTIAL = 206;

    let m, template, statuses;

    // Modal instance
    m = document.querySelector('#upload-modal');

    // Progress bar template
    template = m.querySelector('#progress-bar-template').innerHTML;

    // Progress statuses labels
    statuses = [ 'WAITING', 'UPLOADING', 'MOVING', 'DONE' ];

    /**
     * Render progress bar
     *
     * @param file
     * @returns {string}
     */
    let renderProgressBar = function (file) {

        file.status = statuses[PROGRESS_WAITING];
        return template.replace(/%(\w+)%/g, function (item, p1) {
            return file[p1] || item;
        });

    };

    /**
     * Makes fetch request.
     *
     * @param {String} url
     * @param {Object} options
     * @param {Function} callback
     */
    let request = (url, options, callback) => {

        let defaults = {
            'method': 'POST',
            'headers': {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        };

        fetch(url, {...defaults, ...options})
            .then((response) => { return response.json(); })
            .then(callback)
    };

    /**
     * Moves uploaded file to persistent storage.
     *
     * @param {String} url
     * @param {Object} data
     * @param {Function} callback
     */
    let moveUploadedFile = function (url, data, callback) {

        let options = {
            'mode': 'cors',
            'body': JSON.stringify({
                'name': data.name,
                'uuid': data.uuid,
            }),
        };

        request(url, options, callback);
    };

    /**
     * Creates storage file entity in database.
     *
     * @param {String} url
     * @param {Object} data
     * @param {Function} callback
     */
    let createFileEntity = function (url, data, callback) {

        let options = {
            'method': 'PUT',
            'body': JSON.stringify({
                'name': data.name.replace(/\.[\d]{10,}$/, ''),
                'size': data.size,
                'type': data.type,
                'hash': data.hash,
            })
        };

        request(url, options, callback);

    };

    /**
     * Sets status bar status.
     *
     * @param {Element} progress
     * @param {Number} status
     */
    let setProgressStatus = (progress, status) => {

        status = status ? status : 0;

        // Set progress bar status text
        progress.querySelector('.file__status').innerHTML = statuses[status];

        // Set progress bar status color
        progress.querySelector('.progress__bar').classList.toggle(
            'progress__bar--success', status === 3
        );

    };

    /**
     * Callback function for createFileEntity method.
     *
     * @param {Object} json
     * @param {Element} progress
     * @param {String} url
     */
    let createFileEntityCallback = (json, progress, url) => {

        // Update progress status
        setProgressStatus(progress, PROGRESS_MOVING);

        // Moving uploaded file to persistent storage
        moveUploadedFile(url, json, () => {

            // Update progress status
            setProgressStatus(progress, PROGRESS_DONE);

            // Update number of files left
            let counter = parseInt(m.querySelector('[data-total]').innerHTML) - 1;

            m.querySelector('[data-total]').innerHTML = counter.toString();

            if (counter === 0) {
                update.load(json.container, json.url, false);
            }

        });

    };

    /**
     * Before upload event handler
     *
     * @param event
     * @returns {boolean}
     */
    uploader.beforeUpload = function (event) {

        let files = event.target.files,
            body = m.querySelector('.modal__body');

        // If no files selected stop uploading
        if (files.length === 0) {
            return false;
        }

        // Clear modal body
        body.innerHTML = null;

        // Insert progress bars for each selected file
        for (let i = 0; i < files.length; i++) {
            body.innerHTML += renderProgressBar(files[i]);
        }

        m.querySelector('[data-total]').innerHTML = files.length;

        // Displays modal
        modal.show(m);

        return true;
    };

    /**
     * On upload progress event handler
     *
     * @param event
     */
    uploader.onProgress = function (event) {

        let request, response, progress, percent;

        // Request instance
        request = event.detail.xhr;

        // Response instance
        response = JSON.parse(request.response);

        // Progress bar object
        progress = m.querySelector('[data-file="' + response.name + '"]');

        // Progress percent
        percent = Math.ceil(response.end * 100 / response.size);

        // Set width depends on percent value
        progress.querySelector('.progress__bar').style.width = (percent > 100 ? 100 : percent) + '%';

        if (request.status === RESPONSE_PARTIAL) {
            setProgressStatus(progress, PROGRESS_UPLOADING);
        }

        if (request.status === RESPONSE_OK) {
            let target = document.querySelector('input[type="file"]');

            createFileEntity(target.dataset.create, response, (json) => {
                json.name = response.name;
                createFileEntityCallback(json, progress, target.dataset.move)
            });
        }

        return request.status;
    };

    /**
     * After fetching content
     */
    update.onAfterLoad = () => { updateWidget(); };

    /**
     * Before fetching content
     *
     * @param event
     */
    update.onBeforeLoad = (event) => {
        event.target.insertAdjacentHTML("beforeend", '<div class="spreadsheet__backstage"></div>');
    };

    /**
     * Updates a widget.
     */
    let updateWidget = function () {

        let widget = document.querySelector('[data-widget="space"]');

        if (widget) {
            let options = {
                'method': 'GET',
                'mode': 'cors',
                'headers': {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            };

            fetch(widget.dataset.url, options)
                .then((response) => { return response.json(); })
                .then((data) => {
                    widget.innerHTML = widget.innerHTML.replace(/{(\w+)}/g, (match, p1) => {
                        return formatSize(data[p1], 2);
                    })
                })
        }

    };

    /**
     * Convert integer values to file size format.
     *
     * @param {Number} value
     * @param {Number} decimals
     * @returns {string}
     */
    let formatSize = function (value, decimals) {
        let formatBase = 1024;
        let maxPosition = 4;
        let position = 0;

        do {
            if (Math.abs(value) < formatBase) {
                break;
            }
            value /= formatBase;
            position++;
        } while (position < maxPosition + 1);

        value = +value.toFixed(decimals);

        switch (position) {
            case 0:
                return value + ' B';
            case 1:
                return value + ' KB';
            case 2:
                return value + ' MB';
            case 3:
                return value + ' GB';
            case 4:
                return value + ' TB';
            default:
                return value + ' PT';
        }
    };

    // Update a widget on page load.
    updateWidget();

    // Initialize components
    [update, modal, spreadsheet, uploader].forEach((component) => {
        component.init();
    });
})();
