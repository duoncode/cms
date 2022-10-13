import { plurals } from './plurals';

type GettextOptions = {
    locale: string,
    domain: string,
    contextDelimiter?: string,
};

interface Language {
    [id: string]: string | string[] | {
        language: string,
        "plural-forms"?: string,
    };
}

export default class Gettext {
    locale: string;
    domain: string;
    contextDelimiter: string;
    dictionary: Language;

    constructor(options?: GettextOptions) {
        this.locale = options.locale;
        this.domain = options.domain
        this.contextDelimiter = options.contextDelimiter || String.fromCharCode(4); // \u0004
    }

    expandLocale(locale: string): string[] {
        let locales = [locale];
        let i = locale.lastIndexOf('-');

        while (i > 0) {
            locale = locale.slice(0, i);
            locales.push(locale);
            i = locale.lastIndexOf('-');
        }
        return locales;
    }

    dcnpgettext(
        domain: string | null,
        context: string | null,
        msgid: string,
        msgidPlural: string | null,
        n: number | null
    ): string {
        domain = domain || this.domain;

        if ('string' !== typeof msgid)
            throw new Error(`Msgid "${msgid}" is not a valid translatable string`);

        let translation: string;
        let options = { plural_form: false };
        let key = context ? context + this.contextDelimiter + msgid : msgid;
        let exist: boolean;
        let locale: string;
        let locales = this.expandLocale(this.locale);

        for (var i in locales) {
            locale = locales[i];
            exist = this.dictionary[domain] &&
                this.dictionary[domain][locale] &&
                this.dictionary[domain][locale][key];

            // because it's not possible to define both a singular and a plural form of the same msgid,
            // we need to check that the stored form is the same as the expected one.
            // if not, we'll just ignore the translation and consider it as not translated.
            if (msgidPlural) {
                exist = exist && "string" !== typeof _dictionary[domain][locale][key];
            } else {
                exist = exist && "string" === typeof _dictionary[domain][locale][key];
            }
            if (exist) {
                break;
            }
        }

        if (!exist) {
            translation = msgid;
            options.plural_func = defaults.plural_func;
        } else {
            translation = _dictionary[domain][locale][key];
        }

        // Singular form
        if (!msgidPlural)
            return t.apply(this, [[translation], n, options].concat(Array.prototype.slice.call(arguments, 5)));

        // Plural one
        options.plural_form = true;
        return t.apply(this, [exist ? translation : [msgid, msgidPlural], n, options].concat(Array.prototype.slice.call(arguments, 5)));
    }

    // loadJson() {
    // if (!_.isObject(jsonData))
    // jsonData = JSON.parse(jsonData);
    //
    // if (!jsonData[''] || !jsonData['']['language'] || !jsonData['']['plural-forms'])
    // throw new Error('Wrong JSON, it must have an empty key ("") with "language" and "plural-forms" information');
    //
    // var headers = jsonData[''];
    // delete jsonData[''];
    //
    // return this.setMessages(domain || defaults.domain, headers['language'], jsonData, headers['plural-forms']);
    // }


}
