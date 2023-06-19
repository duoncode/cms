import { get } from 'svelte/store';
import { system, type Locale } from '$lib/sys';
import type { Field, SimpleField, ImageField } from '$types/fields';
import type {
    File,
    Document,
    TextData,
    FileData,
    TranslatedFile,
} from '$types/data';

function fillTranslatedImageField(data: FileData, locales: Locale[]) {
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
        console.log(file);
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

function fillTextField(data: TextData, field: SimpleField, locales: Locale[]) {
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
                content[field.name] = fillTextField(
                    fieldData as TextData,
                    field as SimpleField,
                    locales,
                );
                break;
        }
    });

    return content;
}
