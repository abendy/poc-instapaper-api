/**
 * @file
 * Sitewide javascript behaviors.
 */
(function (window, document) {
    'use strict';

    document.querySelector("#hamburger").addEventListener("click", function() {

        var dataPower = this.getAttribute("data-power");

        if (dataPower == 'on') {
            this.removeAttribute("data-power");
            this.textContent = 'menu';
        } else {
            this.setAttribute("data-power", "on");
            this.textContent = 'close';
        }

    });

})(this, this.document);
