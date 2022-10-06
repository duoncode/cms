import { plurals } from './plurals';

type GettextOptions = {
    locale: string,
    domain: string,
    contextDelimiter?: string,
};

interface Language {
    '': {
        language: string,
        "plural-forms"?: string,
    } | undefined;
    [id: string]: string | string[];
}

export default class Gettext {
    locale: string;
    domain: string
    contextDelimiter: string;

    constructor(options?: GettextOptions) {
        this.locale = options.locale;
        this.domain = options.domain
        this.contextDelimiter = options.contextDelimiter || String.fromCharCode(4); // \u0004
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
