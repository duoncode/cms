import { readable } from 'svelte/store';
import Gettext from './gettext';

const i18n = new Gettext();
const locale = readable<string>(settings.locale);
const locales = readable<string>(settings.locales);


// to avoid xgettext parsing problems define function as const
const __ = i18n.gettext;
// const _n = i18n.ngettext;
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

    i18n.loadJson(messages);
}

function getLocales() {
    return [...locales];
}

export {
    __, _n, initGettext, locale, locales, getLocales
};
