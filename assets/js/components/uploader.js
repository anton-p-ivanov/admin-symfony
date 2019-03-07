
(function (root, factory) {
    // noinspection JSUnresolvedVariable
    if ( typeof define === 'function' && define.amd ) {
        // noinspection JSUnresolvedFunction
        define(factory);
    }
    else if ( typeof exports === 'object' ) {
        module.exports = factory();
    }
    else {
        root.uploader = factory();
    }
})(this, function () {

    'use strict';

    /**
     * Variables
     */

    let

        // Object for public APIs
        uploader = {},

        // Feature test
        supports =  'File' in window &&
                    'FileReader' in window &&
                    'FileList' in window &&
                    'Blob' in window,

        // Placeholder variables
        settings = {},

        // Events
        events = {'beforeUpload': null, 'afterUpload': null, 'onProgress': null, 'onComplete': null},

        // XMLHttpRequest instance
        xhr = new XMLHttpRequest(),

        // File DOM object
        input = null,

        // Default settings
        defaults = {
            selector: '[data-toggle="uploader"]'
        };

    /**
     * Handle events
     * @private
     */
    let eventHandler = function (event) {

        let toggle = event.target;

        while (toggle !== document) {
            let container = toggle.closest(settings.selector);
            if (container) {
                event.preventDefault();
                event.stopImmediatePropagation();

                input.click();

                return;
            }

            toggle = toggle.parentNode;
        }

    };

    /**
     * Handle upload events
     * @private
     */
    let uploadHandler = function () {

        // Trigger beforeUpload custom event
        input.dispatchEvent(events.beforeUpload);

        let chunks = [];

        // Set chunk size in bytes
        const CHUNK_SIZE = 1000 * 1000;

        // Set XMLHttpRequest onload handler
        xhr.onload = function () {

            input.dispatchEvent(events.onProgress);

            // If response status is 206 (partial content)
            // continue uploading chunks
            if (this.status === 206 && chunks.length > 0) {
                upload(chunks);
            }

            // If all chunks successfully uploaded
            else if (this.status === 200) {
                if (chunks.length === 0) {
                    input.dispatchEvent(events.onComplete);
                }
                else {
                    upload(chunks);
                }
            }

        };

        for (let i = 0; i < input.files.length; i++) {

            let file = input.files[i];

            const TOTAL_SIZE = file.size;

            let start = 0,
                end = CHUNK_SIZE;

            while (start < TOTAL_SIZE) {
                chunks.push({
                    'data': slice(file, start, end),
                    'start': start,
                    'end': end,
                    'total': TOTAL_SIZE,
                    'name': file.name + '.' + file.lastModified,
                    // 'timestamp': ,
                    'type': file.type
                });

                start = end;
                end = start + CHUNK_SIZE;
                end = end > TOTAL_SIZE ? TOTAL_SIZE : end;
            }
        }

        // Start upload selected file
        upload(chunks);
    };

    /**
     * Make file.slice method cross-browser
     *
     * @param {Blob} file
     * @param {Number} start
     * @param {Number} end
     * @returns {Blob}
     */
    let slice = function (file, start, end) {
        let slice = 'mozSlice' in file ? file.mozSlice :
            ('webkitSlice' in file ? file.webkitSlice :
            ('slice' in file ? file.slice : () => {}));

        return slice.bind(file)(start, end);
    };

    /**
     * Uploads a first chunk in chunks array
     *
     * @param {Array} chunks
     */
    let upload = function (chunks) {

        // Get first chunk in chunks array
        let chunk = chunks.shift();

        xhr.open("POST", input.dataset.upload, true);
        xhr.setRequestHeader('Content-Range', 'bytes=' + chunk.start + '-' + chunk.end + '/' + chunk.total);
        xhr.setRequestHeader('Content-Disposition', "attachment; filename*=UTF-8''" + encodeURIComponentRFC5987(chunk.name));

        let fd = new FormData();
        fd.append('type', chunk.type);
        fd.append('timestamp', chunk.timestamp);
        fd.append('chunk', chunk.data);

        xhr.send(fd);

    };

    /**
     * Encode string to RCF5987-compliant encoded string.
     *
     * @param str
     * @see https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent
     * @returns {string}
     */
    let encodeURIComponentRFC5987 = (str) => {

        return encodeURIComponent(str)
            . replace(/['()]/g, escape)
            . replace(/\*/g, '%2A')
            . replace(/%(?:7C|60|5E)/g, unescape);

    };

    /**
     * Before upload event handler
     * @param event
     */
    uploader.beforeUpload = function (event) { };

    /**
     * Before upload event handler
     * @param event
     */
    uploader.afterUpload = function (event) { };

    /**
     * On progress upload event handler
     * @param event
     */
    uploader.onProgress = function (event) { };

    /**
     * On complete upload event handler
     * @param event
     */
    uploader.onComplete = function (event) { };

    /**
     * Destroy the current initialization.
     * @public
     */
    uploader.destroy = function () {

        // If plugin isn't already initialized, stop
        if ( !settings ) return;

        // Remove event listeners
        input.removeEventListener('change', uploadHandler, false);
        document.removeEventListener('click', eventHandler, false);

        // Reset variables
        settings = null;

    };

    /**
     * Initialize Plugin
     * @public
     * @param {Object} options User settings
     */
    uploader.init = function ( options ) {

        // feature test
        if ( !supports ) return;

        // Check for required file input
        input = document.querySelector('input[type="file"]');

        if ( !input ) return;

        // Destroy any existing initializations
        uploader.destroy();

        // Merging user options with defaults
        settings = {...defaults, ...options};

        // Listen for file select
        input.addEventListener('change', uploadHandler, false);

        // Init custom events
        for (let e in events) {
            // Create event instance
            events[e] = new CustomEvent(e, {'detail': {xhr: xhr}});

            // Listen for event
            input.addEventListener(e, this[e], false);
        }

        // Listen for click events
        document.addEventListener('click', eventHandler, false);
    };

    //
    // Public APIs
    //

    return uploader;

});