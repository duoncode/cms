import { readable } from 'svelte/store';
import I18N from '../vendor/gettext.esm.js';

const locales = readable(window.GLOBAL_SETTINGS.locales);
const locale = window.GLOBAL_SETTINGS.locale;

window._GLOBAL_I18N_ = null;

// to avoid xgettext parsing problems define function as const
const _ = (msg, ...args) => {
    return window._GLOBAL_I18N_.gettext(msg, ...args);
};

const ngettext = (msg, nmsg, number, ...args) => {
    return window._GLOBAL_I18N_.ngettext(msg, nmsg, number, ...args);
};

async function initGettext() {
    if (window._GLOBAL_I18N_ === null) {
        window._GLOBAL_I18N_ = new I18N();

        let response = await fetch(`/locale/${locale}/messages.json`);
        let appMessages = await response.json();
        let themeMessages = {};

        response = await fetch(`/theme/locale/${locale}/messages.json`);

        if (response.ok) {
            themeMessages = await response.json();
        }

        let messages = Object.assign(appMessages, themeMessages);

        window._GLOBAL_I18N_.loadJSON(messages);
        window._GLOBAL_I18N_.setLocale(locale);
    }
}

function getLocales() {
    return [...window.GLOBAL_SETTINGS.locales];
}

export { _, ngettext, initGettext, locale, locales, getLocales };
