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
        root.pjax = factory();
    }
})(this, function () {

    'use strict';

    //
    // Variables
    //

    let pjax, supports, settings, defaults;

    // Object for public APIs
    pjax = {};

    // Feature test
    supports = !!(window.history && history.pushState);

    // Placeholder variables
    settings = {};

    // Default settings
    defaults = {
        selector: '[data-ajax="true"]',
        options: {
            'headers': {'X-Requested-With': 'XMLHttpRequest'}
        }
    };

    /**
     * Methods
     */
    pjax.load = function (id, url, push) {
        let container = document.getElementById(id);

        if (!container) {
            return false;
        }

        fetch(url, settings.options)
            .then((response) => { return response.text() })
            .then((html) => {
                container.innerHTML = html;
                if (push) {
                    history.pushState({'container': id}, null, url);
                }
            });
    };

    /**
     * Handle click events
     * @private
     */
    let eventHandler = function (event) {

        let target = event.target;

        while (target !== document) {
            let container = target.dataset.toggle === 'ajax'
                ? document.querySelector(target.dataset.container)
                : target.closest(settings.selector);

            if (target.matches('a') && container) {
                // Skip links with [data-ajax="false"]
                if (target.dataset.ajax === "false") {
                    return;
                }

                // Prevent default behavior
                event.preventDefault();
                event.stopImmediatePropagation();

                let enableHistory = (typeof container.dataset.history === 'undefined' || container.dataset.history === "true");

                // Saving initial page state if empty
                if (enableHistory && !history.state) {
                    history.replaceState({'container': container.id}, null, location.href);
                }

                // Fetching page content
                pjax.load(container.id, target.href, enableHistory);

                return;
            }

            target = target.parentNode;
        }

    };

    /**
     * Handle popstate events
     * @private
     */
    let popStateHandler = function (event) {

        let state = event.state;

        if (state && state.container) {
            pjax.load(state.container, location.href, false);
        }

    };

    /**
     * Destroy the current initialization.
     * @public
     */
    pjax.destroy = function () {

        // If plugin isn't already initialized, stop
        if ( !settings ) return;

        // Remove event listeners
        document.removeEventListener('click', eventHandler, false);
        window.removeEventListener('popstate', popStateHandler, false);

        // Reset variables
        settings = null;

    };

    /**
     * Initialize Plugin
     * @public
     * @param {Object} options User settings
     */
    pjax.init = function ( options ) {

        // feature test
        if ( !supports ) return;

        // Destroy any existing initializations
        pjax.destroy();

        // Merging user options with defaults
        settings = {...defaults, ...options};

        // Listen for click events
        document.addEventListener('click', eventHandler, false);

        // Listen for popstate event
        window.addEventListener('popstate', popStateHandler, false);
    };

    //
    // Public APIs
    //

    return pjax;

});