
import modal from "./components/modal";
import pjax from "./components/pjax";
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
                'name': data.name,
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
                pjax.load('p-spreadsheet', location.href, false);
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
        progress.querySelector('.progress__bar').style.width = percent + '%';

        if (request.status === RESPONSE_PARTIAL) {
            setProgressStatus(progress, PROGRESS_UPLOADING);
        }

        if (request.status === RESPONSE_OK) {
            createFileEntity(event.target.dataset.create, response, (json) => {
                createFileEntityCallback(json, progress, event.target.dataset.move)
            });
        }

        return request.status;
    };

    // Init uploader component
    uploader.init();

})();
