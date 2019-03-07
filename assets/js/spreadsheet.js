require('../css/spreadsheet.scss');

import pjax from "./components/pjax";
import modal from "./components/modal";
import spreadsheet from "./components/spreadsheet";

(function () {
    'use strict';

    pjax.init();
    modal.init();
    spreadsheet.init();

})();
