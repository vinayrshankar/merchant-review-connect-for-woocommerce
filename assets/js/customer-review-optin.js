/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */
(function (window) {
    'use strict';

    var state = window.MRCReviewConnect || {};

    if (state.locale) {
        window.___gcfg = { lang: state.locale };
    }

    window.MRCReviewConnectReady = function () {
        if (!window.gapi || typeof window.gapi.load !== 'function' || !state.payload) {
            return;
        }

        window.gapi.load('surveyoptin', function () {
            if (!window.gapi.surveyoptin || typeof window.gapi.surveyoptin.render !== 'function') {
                return;
            }
            window.gapi.surveyoptin.render(state.payload);
        });
    };
}(window));
