require('../../css/components/dropdowns.scss');

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
        root.dropdown = factory();
    }
})(this, function () {

    'use strict';

    //
    // Variables
    //
    let dropdown, supports, settings, defaults, initialized;

    // Object for public APIs
    dropdown = {};

    // Feature test
    supports = !!document.querySelector && !!document.addEventListener;

    // Placeholder variables
    settings = {};

    // Default settings
    defaults = {
        selector: '[data-toggle="dropdown"]'
    };

    // Is plugin initialized
    initialized = false;

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

                let sibling = container.nextElementSibling;
                if (sibling && sibling.classList.contains('dropdown__menu')) {
                    container.classList.toggle('dropdown__toggle--active', true);
                    sibling.classList.toggle('dropdown__menu--active', true);

                    document.addEventListener('click', closeHandler, false);
                }

                return;
            }

            toggle = toggle.parentNode;
        }

    };

    /**
     * Close event handler
     * @private
     */
    let closeHandler = function (event) {

        let target = event.target;

        if (!target.closest('.dropdown__menu')) {
            let visible = document.querySelectorAll('.dropdown__menu--active');

            [].forEach.call(visible, (item) => {
                item.classList.toggle('dropdown__menu--active', false);
                item.previousElementSibling.classList.toggle('dropdown__toggle--active', false);
            });

            document.removeEventListener('click', closeHandler, false);
        }
    };

    /**
     * Destroy the current initialization.
     * @public
     */
    dropdown.destroy = function () {

        // If plugin isn't already initialized, stop
        if ( !initialized ) return;

        // Remove event listeners
        document.removeEventListener('click', eventHandler, false);
        document.removeEventListener('click', closeHandler, false);

        // Reset variables
        settings = {};
        initialized = false;
    };

    /**
     * Initialize Plugin
     * @public
     * @param {Object} options User settings
     */
    dropdown.init = function ( options ) {

        // feature test
        if ( !supports ) return;

        // Destroy any existing initializations
        dropdown.destroy();

        // Merging user options with defaults
        settings = {...defaults, ...options};

        // Listen for click events
        document.addEventListener('click', eventHandler, false);

        initialized = true;
    };

    //
    // Public APIs
    //

    return dropdown;

});