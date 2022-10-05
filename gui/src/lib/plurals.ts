type Plural = {
    nplurals: 1 | 2 | 3 | 4 | 5 | 6,
    fn: (n: number) => boolean | 0 | 1 | 2 | 3 | 4 | 5,
    text: string,
};

type Plurals = Record<string, Plural>;

function p(locales: string[], plural: Plural): Plurals {
    return Object.fromEntries(locales.map((k: string) => [k, plural]));
}

const plurals: Plurals = {
    ...p([
        'ay', 'bo', 'cgg', 'dz', 'fa', 'id', 'ja', 'jbo',
        'ka', 'kk', 'km', 'ko', 'ky', 'lo', 'ms', 'my',
        'sah', 'su', 'th', 'tt', 'ug', 'vi', 'wo', 'zh'
    ], {
        nplurals: 1,
        fn: (_: number) => { return 0 },
        text: 'nplurals = 1; plural = 0',
    }),
    ...p([
        'ach', 'ak', 'am', 'arn', 'br', 'fil', 'fr', 'gun',
        'ln', 'mfe', 'mg', 'mi', 'oc', 'tg', 'ti', 'tr',
        'uz', 'wa'
    ], {
        nplurals: 2,
        fn: (n: number) => n > 1,
        text: 'nplurals = 2; plural = (n > 1)',
    }),
    ...p([
        'af', 'an', 'ast', 'az', 'bg', 'bn', 'brx',
        'ca', 'da', 'de', 'doi', 'el', 'en', 'eo',
        'es', 'et', 'eu', 'ff', 'fi', 'fo', 'fur',
        'fy', 'gl', 'gu', 'ha', 'he', 'hi', 'hne',
        'hu', 'hy', 'it', 'kn', 'ku', 'lb', 'mai',
        'ml', 'mn', 'mni', 'mr', 'nah', 'nap', 'nb',
        'ne', 'nl', 'nn', 'no', 'nso', 'or', 'pa',
        'pap', 'pms', 'ps', 'pt', 'rm', 'rw', 'sat',
        'sco', 'sd', 'se', 'si', 'so', 'son', 'sq',
        'sv', 'sw', 'ta', 'te', 'tk', 'ur', 'yo'
    ], {
        nplurals: 2,
        fn: (n: number) => n !== 1,
        text: 'nplurals = 2; plural = (n !== 1)',
    }),
    ...p(['jv'], {
        nplurals: 2,
        fn: (n: number) => n !== 0,
        text: 'nplurals = 2; plural = (n !== 0)',
    }),
    ...p(['is'], {
        nplurals: 2,
        fn: (n: number) => n % 10 !== 1 || n % 100 === 11,
        text: 'nplurals = 2; plural = (n % 10 !== 1 || n % 100 === 11)',
    }),
    ...p(['mk'], {
        nplurals: 2,
        fn: (n: number) => n === 1 || n % 10 === 1 ? 0 : 1,
        text: 'nplurals = 2; plural = (n === 1 || n % 10 === 1 ? 0 : 1)',
    }),
    ...p(['lv'], {
        nplurals: 3,
        fn: (n: number) => n % 10 === 1 && n % 100 !== 11 ? 0 : n !== 0 ? 1 : 2,
        text: 'nplurals = 3; plural = (n % 10 === 1 && n % 100 !== 11 ? 0 : n !== 0 ? 1 : 2)',
    }),
    ...p(['lt'], {
        nplurals: 3,
        fn: (n: number) => n % 10 === 1 && n % 100 !== 11 ? 0 : n % 10 >= 2 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2,
        text: 'nplurals = 3; plural = (n % 10 === 1 && n % 100 !== 11 ? 0 : n % 10 >= 2 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2)',
    }),
    ...p(['be', 'bs', 'hr', 'ru', 'sr', 'uk'], {
        nplurals: 3,
        fn: (n: number) => n % 10 === 1 && n % 100 !== 11 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2,
        text: 'nplurals = 3; plural = (n % 10 === 1 && n % 100 !== 11 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2)',
    }),
    ...p(['mnk'], {
        nplurals: 3,
        fn: (n: number) => n === 0 ? 0 : n === 1 ? 1 : 2,
        text: 'nplurals = 3; plural = (n === 0 ? 0 : n === 1 ? 1 : 2)',
    }),
    ...p(['ro'], {
        nplurals: 3,
        fn: (n: number) => n === 1 ? 0 : (n === 0 || (n % 100 > 0 && n % 100 < 20)) ? 1 : 2,
        text: 'nplurals = 3; plural = (n === 1 ? 0 : (n === 0 || (n % 100 > 0 && n % 100 < 20)) ? 1 : 2)',
    }),
    ...p(['cs', 'sk'], {
        nplurals: 3,
        fn: (n: number) => n === 1 ? 0 : (n >= 2 && n <= 4) ? 1 : 2,
        text: 'nplurals = 3; plural = (n === 1 ? 0 : (n >= 2 && n <= 4) ? 1 : 2)',
    }),
    ...p(['csb', 'pl'], {
        nplurals: 3,
        fn: (n: number) => n === 1 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2,
        text: 'nplurals = 3; plural = (n === 1 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2)',
    }),
    ...p(['gd'], {
        nplurals: 4,
        fn: (n: number) => (n === 1 || n === 11) ? 0 : (n === 2 || n === 12) ? 1 : (n > 2 && n < 20) ? 2 : 3,
        text: 'nplurals = 4; plural = ((n === 1 || n === 11) ? 0 : (n === 2 || n === 12) ? 1 : (n > 2 && n < n % 100 === 1 ? 0 : n % 100 === 2 ? 1 : n % 100 === 3 || n % 100 === 4 ? 2 : 320) ? 2 : 3)',
    }),
    ...p(['sl'], {
        nplurals: 4,
        fn: (n: number) => n % 100 === 1 ? 0 : n % 100 === 2 ? 1 : n % 100 === 3 || n % 100 === 4 ? 2 : 3,
        text: 'nplurals = 4; plural = (n % 100 === 1 ? 0 : n % 100 === 2 ? 1 : n % 100 === 3 || n % 100 === 4 ? 2 : 3)',
    }),
    ...p(['mt'], {
        nplurals: 4,
        fn: (n: number) => (n === 1 ? 0 : n === 0 || (n % 100 > 1 && n % 100 < 11) ? 1 : (n % 100 > 10 && n % 100 < 20) ? 2 : 3),
        text: 'nplurals = 4; plural = (n === 1 ? 0 : n === 0 || ( n % 100 > 1 && n % 100 < 11) ? 1 : (n % 100 > 10 && n % 100 < 20 ) ? 2 : 3)',
    }),
    ...p(['cy'], {
        nplurals: 4,
        fn: (n: number) => n === 1 ? 0 : n === 2 ? 1 : (n !== 8 && n !== 11) ? 2 : 3,
        text: 'nplurals = 4; plural = (n === 1 ? 0 : n === 2 ? 1 : (n !== 8 && n !== 11) ? 2 : 3)',
    }),
    ...p(['kw'], {
        nplurals: 4,
        fn: (n: number) => n === 1 ? 0 : n === 2 ? 1 : n === 3 ? 2 : 3,
        text: 'nplurals = 4; plural = (n === 1 ? 0 : n === 2 ? 1 : n === 3 ? 2 : 3)',
    }),
    ...p(['ga'], {
        nplurals: 5,
        fn: (n: number) => n === 1 ? 0 : n === 2 ? 1 : n < 7 ? 2 : n < 11 ? 3 : 4,
        text: 'nplurals = 5; plural = (n === 1 ? 0 : n === 2 ? 1 : n < 7 ? 2 : n < 11 ? 3 : 4)',
    }),
    ...p(['ar'], {
        nplurals: 6,
        fn: (n: number) => n === 0 ? 0 : n === 1 ? 1 : n === 2 ? 2 : n % 100 >= 3 && n % 100 <= 10 ? 3 : n % 100 >= 11 ? 4 : 5,
        text: 'nplurals = 6; plural = (n === 0 ? 0 : n === 1 ? 1 : n === 2 ? 2 : n % 100 >= 3 && n % 100 <= 10 ? 3 : n % 100 >= 11 ? 4 : 5)',
    }),
};
