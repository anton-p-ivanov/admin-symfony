require('../../css/components/modal.scss');

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
        root.modal = factory();
    }
})(this, function () {

    'use strict';

    //
    // Variables
    //

    // Object for public APIs
    let modal = {
        target: null,
        visible: false
    };

    let supports = !!document.querySelector && !!document.addEventListener; // Feature test
    let settings; // Placeholder variables

    // Default settings
    let defaults = {
        selector: '[data-toggle="modal"]',
        close: '[data-dismiss="modal"]'
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

                modal.target = document.querySelector('.modal');
                if (modal.target) {
                    if (container.dataset.remote) {
                        fetch(container.href, {'headers': {'X-Requested-With': 'XMLHttpRequest'}})
                            .then((response) => { return response.text() })
                            .then((html) => {
                                let container = modal.target.querySelector('.modal__container');

                                if (container) {
                                    container.innerHTML = html;
                                    modal.toggle();
                                }
                            });
                    }
                    else {
                        modal.toggle();
                    }
                }

                return;
            }

            toggle = toggle.parentNode;
        }

    };

    /**
     * Handle closing events
     * @private
     */
    let closeHandler = function (event) {

        let toggle = event.target;

        while (toggle !== modal.target) {
            if (toggle.closest(settings.close)) {
                event.preventDefault();
                event.stopImmediatePropagation();

                modal.toggle();

                return;
            }

            toggle = toggle.parentNode;
        }
    };

    /**
     * Handle form submit events
     * @private
     */
    let submitHandler = function (event) {

        let toggle = event.target;

        while (toggle !== modal.target) {
            if (toggle.matches('form')) {
                event.preventDefault();
                event.stopImmediatePropagation();

                let options = {
                    'method': toggle.method,
                    'body': new FormData(toggle),
                    'headers': {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                };

                fetch(toggle.action, options)
                    .then((response) => {
                        if (response.status === 200) {
                            modal.toggle();

                            let options = {
                                'headers': {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            };

                            fetch(toggle.dataset.url, options)
                                .then((response) => { return response.text(); })
                                .then((content) => {
                                    let container = document.querySelector(toggle.dataset.container);
                                    if (container) {
                                        container.style.minHeight = container.offsetHeight + 'px';
                                        container.innerHTML = content;
                                    }
                                });

                            return null;
                        }

                        return response.text();
                    })
                    .then((data) => {
                        if (data && modal.target) {
                            modal.target.querySelector('.modal__container').innerHTML = data.toString();
                        }
                    });

                return;
            }

            toggle = toggle.parentNode;
        }
    };

    /**
     * Handle keyboard events
     * @private
     */
    // let keyboardHandler = function (event) {
    //     if (event.key === "Escape" && modal.target) {
    //         modal.toggle();
    //     }
    // };

    let events = {
        'click': closeHandler,
        'submit': submitHandler
    };

    /**
     * Toggle modal components.
     * @public
     */
    modal.toggle = function () {

        // Toggle modal visibility
        modal.target.classList.toggle('modal__active', !modal.visible);

        // Toggle backdrop visibility
        document.querySelector('.modal-backdrop').classList.toggle('modal-backdrop__active', !modal.visible);

        // Toggle body class
        document.body.classList.toggle('no-scroll', !modal.visible);

        if (modal.visible) {
            for (let index in events) {
                modal.target.removeEventListener(index, events[index], false)
            }
            modal.target = null;
        }
        else {
            for (let index in events) {
                modal.target.addEventListener(index, events[index], false)
            }
        }

        modal.visible = !modal.visible;
    };

    /**
     * Destroy the current initialization.
     * @public
     */
    modal.destroy = function () {

        // If plugin isn't already initialized, stop
        if ( !settings ) return;

        // Remove event listeners
        document.removeEventListener('click', eventHandler, false);

        if (modal.target) {
            for (let index in events) {
                modal.target.removeEventListener(index, events[index], false)
            }
        }

        // Reset variables
        settings = null;

        modal.target = null;
        modal.visible = false;
    };

    /**
     * Initialize Plugin
     * @public
     * @param {Object} options User settings
     */
    modal.init = function ( options ) {

        // feature test
        if ( !supports ) return;

        // Destroy any existing initializations
        modal.destroy();

        // Merging user options with defaults
        settings = {...defaults, ...options};

        // Listen for click events
        document.addEventListener('click', eventHandler, false);
    };

    modal.show = function ( selector ) {
        let target = typeof selector === 'object' ? selector : document.querySelector(selector);

        if (target) {
            modal.target = target;
            modal.toggle();
        }
    };

    //
    // Public APIs
    //

    return modal;

});