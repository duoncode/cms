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
    translateFile: boolean;
}

export interface ImageField extends FileField {
    translateFile: boolean;
}

export interface GridField extends SimpleField {
    columns: number;
    minCellWidth: number;
}

export type Field = ImageField | FileField | SimpleField;
