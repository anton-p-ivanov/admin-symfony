require('../css/spreadsheet.scss');

import update from "./components/update";
import modal from "./components/modal";
import spreadsheet from "./components/spreadsheet";

(function () {
    'use strict';

    update.init();
    modal.init();
    spreadsheet.init();

    update.onBeforeLoad = (event) => {
        event.target.insertAdjacentHTML("beforeend", '<div class="spreadsheet__backstage"></div>');
    };
})();
