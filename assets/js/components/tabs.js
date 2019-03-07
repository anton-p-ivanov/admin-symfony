require('../../css/components/tabs.scss');

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
        root.tabs = factory();
    }
})(this, function () {

    'use strict';

    //
    // Variables
    //

    let tabs = {}; // Object for public APIs
    let supports = !!document.querySelector && !!document.addEventListener; // Feature test
    let settings; // Placeholder variables

    // Default settings
    let defaults = {
        selector: '[data-toggle="tab"]',
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

                let target = document.querySelector(container.dataset.target);
                if (target) {
                    [].forEach.call(toggle.closest('.tabs').querySelectorAll('.tabs__link, .tabs__panel'), (item) => {
                        item.classList.forEach((name) => {
                            if (name.indexOf('--active') !== -1) {
                                item.classList.remove(name);
                            }
                        });
                    });

                    [].forEach.call([toggle, target], (item) => {
                        item.classList.add(item.classList.item(0) + '--active');
                    });

                    if (container.dataset.remote && !target.dataset.loaded) {
                        fetch(container.href, {'headers': {'X-Requested-With': 'XMLHttpRequest'}})
                            .then((response) => { return response.text() })
                            .then((html) => {
                                target.innerHTML = html;
                                target.dataset.loaded = true;
                            });
                    }
                }

                return;
            }

            toggle = toggle.parentNode;
        }

    };

    /**
     * Destroy the current initialization.
     * @public
     */
    tabs.destroy = function () {

        // If plugin isn't already initialized, stop
        if ( !settings ) return;

        // Remove event listeners
        document.removeEventListener('click', eventHandler, false);

        // Reset variables
        settings = null;

    };

    /**
     * Initialize Plugin
     * @public
     * @param {Object} options User settings
     */
    tabs.init = function ( options ) {

        // feature test
        if ( !supports ) return;

        // Destroy any existing initializations
        tabs.destroy();

        // Merging user options with defaults
        settings = {...defaults, ...options};

        // Listen for click events
        document.addEventListener('click', eventHandler, false);

    };

    //
    // Public APIs
    //

    return tabs;

});