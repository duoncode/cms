import { get } from 'svelte/store';
import { system, type Locale } from '$lib/sys';
import type { Field, SimpleField, ImageField, GridField } from '$types/fields';
import type {
    File,
    Document,
    TextData,
    NumberData,
    FileData,
    TranslatedFile,
    GridItem,
    GridData,
} from '$types/data';

function fillTranslatedImageField(data: FileData, locales: Locale[]) {
    if (!data) {
        data = {} as FileData;
    }
    if (data.files === undefined) {
        data.files = {} as Record<string, TranslatedFile>;
    }

    locales.map((locale: Locale) => {
        if (data.files[locale.id] === undefined) {
            data.files[locale.id] = { file: '', alt: '', title: '' };
        }
        if (data.files[locale.id].file === undefined) {
            data.files[locale.id].file = '';
        }
        if (data.files[locale.id].alt === undefined) {
            data.files[locale.id].alt = '';
        }
        if (data.files[locale.id].title === undefined) {
            data.files[locale.id].title = '';
        }
    });

    return data;
}

function fillTranslatedAltField(data: FileData, locales: Locale[]) {
    if (data.files === undefined) {
        data.files = [] as File[];
    }

    (data.files as File[]).map((file: File) => {
        if (file.alt === undefined) {
            file.alt = {};
        }
        if (file.title === undefined) {
            file.title = {};
        }
        locales.map((locale: Locale) => {
            if (file.alt[locale.id] === undefined) {
                file.alt[locale.id] = '';
            }
            if (file.title[locale.id] === undefined) {
                file.title[locale.id] = '';
            }
        });
    });

    return data;
}

function fillImageField(data: FileData, field: ImageField, locales: Locale[]) {
    if (!data) {
        data = {} as FileData;
    }

    if (field.translateImage) {
        return fillTranslatedImageField(data, locales);
    }

    if (field.translate) {
        return fillTranslatedAltField(data, locales);
    }

    data.files = (data.files as File[]).map((file: File) => {
        if (file.file === undefined) {
            file.file = '';
        }

        if (file.alt === undefined) {
            file.alt = '';
        }

        if (file.title === undefined) {
            file.title = '';
        }

        return file;
    });

    return data;
}

function fillNumberField(data: NumberData) {
    if (!data) {
        data = {
            type: 'number',
            value: null,
        };
    }
    if (data.value === undefined) {
        data.value = null;
    }
    return data;
}

function fillTextField(
    data: TextData,
    field: SimpleField,
    locales: Locale[],
    type: string,
) {
    if (!data) {
        data = {
            type: {
                'Conia\\Core\\Field\\Text': 'text',
                'Conia\\Core\\Field\\Html': 'html',
                'Conia\\Core\\Field\\Hidden': 'hidden',
                'Conia\\Core\\Field\\Date': 'date',
                'Conia\\Core\\Field\\Time': 'time',
            }[type],
        } as TextData;
    }

    if (field.translate) {
        if (data.value === undefined) {
            data.value = {};
        }

        locales.map((locale: Locale) => {
            if (data.value[locale.id] === undefined) {
                data.value[locale.id] = '';
            }
        });
    } else {
        if (data.value === undefined) {
            data.value = '';
        }
    }

    return data;
}

function fillGridField(data: GridData, field: SimpleField, locales: Locale[]) {
    if (!data) {
        data = {
            type: 'grid',
            columns: 12,
            i18n: 'separate',
        } as GridData;
    }

    locales.map((locale: Locale) => {
        if (data[locale.id] === undefined) {
            data[locale.id] = [] as GridItem;
        }
    });

    return data;
}

export function fillMissingAttrs(fields: Field[], data: Document) {
    const locales = get(system).locales;
    const content = data.content;

    fields.map((field: Field) => {
        const fieldData = content[field.name];

        switch (field.type) {
            case 'Conia\\Core\\Field\\Picture':
            case 'Conia\\Core\\Field\\Image':
                content[field.name] = fillImageField(
                    fieldData as FileData,
                    field as ImageField,
                    locales,
                );
                break;
            case 'Conia\\Core\\Field\\Text':
            case 'Conia\\Core\\Field\\Html':
            case 'Conia\\Core\\Field\\Hidden':
            case 'Conia\\Core\\Field\\Date':
            case 'Conia\\Core\\Field\\Time':
                content[field.name] = fillTextField(
                    fieldData as TextData,
                    field as SimpleField,
                    locales,
                    field.type,
                );
                break;
            case 'Conia\\Core\\Field\\Number':
                content[field.name] = fillNumberField(fieldData as NumberData);
                break;
            case 'Conia\\Core\\Field\\Grid':
                content[field.name] = fillGridField(
                    fieldData as GridData,
                    field as GridField,
                    locales,
                );
                break;
        }
    });

    return content;
}
