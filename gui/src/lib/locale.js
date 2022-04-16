import { readable } from 'svelte/store';
import I18N from './gettext';
import { getSettings } from './settings';

const i18n = new I18N();
const settings = getSettings();
const locale = readable(settings.locale);
const locales = readable(settings.locales);


// to avoid xgettext parsing problems define function as const
const _ = (msg, ...args) => {
    return i18n.gettext(msg, ...args);
};

const ngettext = (msg, nmsg, number, ...args) => {
    return i18n.ngettext(msg, nmsg, number, ...args);
};

async function initGettext() {
    let response = await fetch(`/locale/${locale}/messages.json`);
    let appMessages = await response.json();
    let themeMessages = {};

    response = await fetch(`/theme/locale/${locale}/messages.json`);

    if (response.ok) {
        themeMessages = await response.json();
    }

    let messages = Object.assign(appMessages, themeMessages);

    i18n.loadJSON(messages);
    i18n.setLocale(locale);
}

function getLocales() {
    return [...locales];
}

export { _, ngettext, initGettext, locale, locales, getLocales };
