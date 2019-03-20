require('../css/users.scss');

(function () {
    'use strict';

    /**
     * Generates random string of given length.
     *
     * @param {Number} length
     * @returns {String}
     */
    let generatePassword = (length) => {

        let charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*~",
            result = "";

        for (let i = 0, n = charset.length; i < length; ++i) {
            result += charset.charAt(Math.floor(Math.random() * n));
        }

        return result;

    };

    /**
     * Event handler.
     *
     * @param {Event} event
     */
    let eventHandler = (event) => {

        let target = event.target;

        while (target !== document) {
            if (target.matches('[data-toggle="password"]')) {
                event.preventDefault();
                event.stopImmediatePropagation();

                let password = generatePassword(10),
                    fields = document.getElementById('extra')
                        .querySelectorAll('input[id^="user_password_"]');

                [].forEach.call(fields, (field) => {
                    field.value = password;
                    field.type = 'text';
                });
            }

            target = target.parentNode;
        }
    };

    document.addEventListener('click', eventHandler, false);
})();
