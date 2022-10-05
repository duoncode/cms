import { readable } from 'svelte/store';
import GetText from './gettext';
import { getSettings } from './boot';

console.log(GetText);

const i18n = GetText();
const settings = getSettings();
const locale = readable(settings.locale);
const locales = readable(settings.locales);


// to avoid xgettext parsing problems define function as const
console.log(i18n);
const __ = i18n.gettext;
const _n = i18n.ngettext;
// const _d = (domain, msgid) => i18n.dcnpgettext(domain, undefined, msgid);
// const _dn = (domain, msgid, msgIdPlural, n) => i18n.dcnpgettext(domain, undefined, msgid, msgIdPlural, n);

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

export {
    __, _n, initGettext, locale, locales, getLocales
};
