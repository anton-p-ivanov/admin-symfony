require('../css/storage.scss');

(function () {
    'use strict';

    let widget = document.querySelector('[data-widget="space"]');

    if (widget) {
        let options = {
            'method': 'GET',
            'mode': 'cors',
            'headers': {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        };

        fetch(widget.dataset.url, options)
            .then((response) => { return response.json(); })
            .then((data) => {
                widget.innerHTML = widget.innerHTML.replace(/{(\w+)}/g, (match, p1) => {
                    return formatSize(data[p1], 2);
                })
            })
    }

    /**
     * Convert integer values to file size format.
     *
     * @param {Number} value
     * @param {Number} decimals
     * @returns {string}
     */
    let formatSize = function (value, decimals) {
        let formatBase = 1024;
        let maxPosition = 4;
        let position = 0;

        do {
            if (Math.abs(value) < formatBase) {
                break;
            }
            value /= formatBase;
            position++;
        } while (position < maxPosition + 1);

        value = +value.toFixed(decimals);

        switch (position) {
            case 0:
                return value + ' B';
            case 1:
                return value + ' KB';
            case 2:
                return value + ' MB';
            case 3:
                return value + ' GB';
            case 4:
                return value + ' TB';
            default:
                return value + ' PT';
        }
    }
})();
