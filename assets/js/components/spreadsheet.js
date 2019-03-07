// require('../../css/components/spreadsheet.scss');

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
        root.spreadsheet = factory();
    }
})(this, function () {

    'use strict';

    //
    // Variables
    //

    let spreadsheet, supports, settings, defaults, initialized;

    // Plugin is initialized
    initialized = false;

    // Object for public APIs
    spreadsheet = {};

    // Feature test
    supports = !!document.querySelector && !!document.addEventListener;

    // Placeholder variables
    settings = {};

    // Default settings
    defaults = {
        selector: '.spreadsheet'
    };

    /**
     * Handle events
     * @private
     */
    let eventHandler = function (event) {

        let toggle = event.target;

        while (toggle !== document) {
            let input = toggle.closest('input[data-toggle]');
            if (input) {
                event.preventDefault();
                event.stopImmediatePropagation();

                let checkboxes = spreadsheet.target.querySelectorAll('tbody input[data-toggle]');

                if (input.closest('thead')) {
                    [].forEach.call(checkboxes, (el) => { el.checked = input.checked; })
                }
                else if (input.closest('tbody')) {
                    spreadsheet.target.querySelector('thead input[data-toggle]').checked = (
                        checkboxes.length === [].filter.call(checkboxes, (el) => { return el.checked; }).length
                    );

                    input.closest('tr').classList.toggle('table__row--selected', input.checked);
                }

                return;
            }

            toggle = toggle.parentNode;
        }

    };

    let keyboardHandler = function (e) {
        if (e.ctrlKey) {
            if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                e.preventDefault();
                e.stopImmediatePropagation();

                let direction = (e.key === 'ArrowRight' ? 'forward' : 'backward'),
                    pager = spreadsheet.target.querySelector('[data-pager="' + direction + '"]');

                if (pager) {
                    pager.click();
                }
            }
        }
    };

    /**
     * Destroy the current initialization.
     * @public
     */
    spreadsheet.destroy = function () {

        // If plugin isn't already initialized, stop
        if ( !initialized ) return;

        // Remove event listeners
        spreadsheet.target.removeEventListener('change', eventHandler, false);
        document.removeEventListener('keyup', keyboardHandler, false);

        // Reset variables
        settings = {};

    };

    /**
     * Initialize Plugin
     * @public
     * @param {Object} options User settings
     */
    spreadsheet.init = function ( options ) {

        // Destroy any existing initializations
        spreadsheet.destroy();

        // Merging user options with defaults
        settings = {...defaults, ...options};

        // Looking for spreadsheet in DOM-tree
        spreadsheet.target = document.querySelector(settings.selector);

        // feature test
        if ( !supports || !spreadsheet.target ) return;

        // Listen for change events
        spreadsheet.target.addEventListener('change', eventHandler, false);

        // Listen for keyboard events
        document.addEventListener('keyup', keyboardHandler, false);

        // Init plugin
        initialized = true;
    };

    //
    // Public APIs
    //

    return spreadsheet;

});