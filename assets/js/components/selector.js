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
        root.selector = factory();
    }
})(this, function () {

    'use strict';

    //
    // Variables
    //

    let selector = {}; // Object for public APIs
    let supports = !!document.querySelector && !!document.addEventListener; // Feature test
    let settings; // Placeholder variables

    // Default settings
    let defaults = {
        selector: '[data-toggle="selection"]',
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
                    let state = target.querySelectorAll('input:checked').length === 0;
                    [].forEach.call(target.querySelectorAll('input[type="checkbox"]'), (item) => {
                        item.checked = state;
                    });
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
    selector.destroy = function () {

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
    selector.init = function ( options ) {

        // feature test
        if ( !supports ) return;

        // Destroy any existing initializations
        selector.destroy();

        // Merging user options with defaults
        settings = {...defaults, ...options};

        // Listen for click events
        document.addEventListener('click', eventHandler, false);

    };

    //
    // Public APIs
    //

    return selector;

});