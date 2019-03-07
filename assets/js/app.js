require('../css/app.scss');

(function () {

    'use strict';

    /**
     * Handle events
     * @private
     */
    let eventHandler = function (event) {

        let toggle = event.target;

        while (toggle !== document) {
            // Skip clicks for disabled elements
            if (toggle.closest('[data-disabled="true"]')) {
                event.preventDefault();
                event.stopImmediatePropagation();

                return;
            }

            toggle = toggle.parentNode;
        }

    };

    document.addEventListener('click', eventHandler, false);

})();
