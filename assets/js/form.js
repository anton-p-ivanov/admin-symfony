require('../css/form.scss');

import tabs from "./components/tabs";
import selector from "./components/selector";
import modal from "./components/modal";
import update from "./components/update";

(function () {
    'use strict';

    tabs.init();
    selector.init();
    modal.init();
    update.init();

})();
