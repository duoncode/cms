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

export interface ImageField extends SimpleField {
    multiple: boolean;
    translateImage: boolean;
}

export type Field = ImageField | SimpleField;
