/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */
(function (window, document) {
    'use strict';

    var state = window.MRCStoreWidget || {};

    function startWidget() {
        if (!window.merchantwidget || typeof window.merchantwidget.start !== 'function') {
            return;
        }
        window.merchantwidget.start(state.options || {});
    }

    if (window.merchantwidget && typeof window.merchantwidget.start === 'function') {
        startWidget();
        return;
    }

    var googleScript = document.getElementById('mrc-google-store-widget-js');
    if (googleScript) {
        googleScript.addEventListener('load', startWidget, { once: true });
    }
}(window, document));
