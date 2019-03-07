require('../css/form.scss');

import tabs from "./components/tabs";
import selector from "./components/selector";
import modal from "./components/modal";
import pjax from "./components/pjax";

(function () {
    'use strict';

    tabs.init();
    selector.init();
    modal.init();
    pjax.init();

})();
