require('../css/app.scss');

import DropDown from "./components/dropdown";

(function () {

    'use strict';

    DropDown.init();

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
