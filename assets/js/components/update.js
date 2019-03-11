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
        root.update = factory();
    }
})(this, function () {

    'use strict';

    //
    // Variables
    //

    let update, supports, settings, defaults;

    // Object for public APIs
    update = {};

    // Feature test
    supports = !!(window.history && history.pushState);

    // Placeholder variables
    settings = {};

    // Default settings
    defaults = {
        selector: '[data-update]',
        options: {
            'headers': {'X-Requested-With': 'XMLHttpRequest'}
        }
    };

    /**
     * Methods
     */
    update.load = function (id, url, push) {
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

            if (target.matches(settings.selector) || target.closest(settings.selector)) {
                let selector = target.dataset.update || target.closest(settings.selector).dataset.update,
                    container = document.querySelector(selector);

                if (!container) {
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
                update.load(container.id, target.href, enableHistory);

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
            update.load(state.container, location.href, false);
        }
    };

    /**
     * Destroy the current initialization.
     * @public
     */
    update.destroy = function () {

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
    update.init = function (options ) {

        // feature test
        if ( !supports ) return;

        // Destroy any existing initializations
        update.destroy();

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

    return update;

});