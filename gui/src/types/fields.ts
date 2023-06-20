export interface SimpleField {
    rows: number | null;
    width: number | null;
    required: boolean;
    description: string | null;
    label: string;
    name: string;
    type: string;
    translate: boolean;
}

export interface FileField extends SimpleField {
    multiple: boolean;
}

export interface ImageField extends FileField {
    translateImage: boolean;
}

export interface GridField extends SimpleField {
    columns: number;
    i18n: 'separate';
}

export type Field = ImageField | FileField | SimpleField;
