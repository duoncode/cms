import { readable } from 'svelte/store';
import i18n from 'gettext.js/lib';
import { settings } from './settings';

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
    let response = await fetch(`/locale/${settings.locale}/messages.json`);
    let appMessages = await response.json();
    let themeMessages = {};

    response = await fetch(`/theme/locale/${settings.locale}/messages.json`);

    if (response.ok) {
        themeMessages = await response.json();
    }

    let messages = Object.assign(appMessages, themeMessages);

    i18n.loadJSON(messages);
    i18n.setLocale(settings.locale);
}

function getLocales() {
    return [...settings.locales];
}

export { _, ngettext, initGettext, locale, locales, getLocales };
