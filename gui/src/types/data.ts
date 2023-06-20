export interface File {
    file?: string;
    alt?: string | Record<string, string>;
    title?: string | Record<string, string>;
}

export interface TranslatedFile {
    file: string;
    alt: string;
    title: string;
}

export interface TextData {
    type: 'text' | 'html' | 'hidden' | 'date' | 'time';
    value?: string | Record<string, string>;
}

export interface NumberData {
    type: 'number';
    value?: number;
}

export interface FileData {
    type: 'picture' | 'image';
    files: File[] | Record<string, TranslatedFile>;
}

export interface GridBase {
    type: string;
    colspan: number;
    rowspan: number;
}

export interface GridHtml extends GridBase {
    type: 'html';
    value: string;
}

export interface GridImage extends GridBase {
    type: 'image';
    files: TranslatedFile[];
}

export interface GridYoutube extends GridBase {
    type: 'youtube';
    id: string;
}

export type GridItem = GridHtml | GridImage | GridYoutube;

export interface GridData {
    type: 'grid';
    i18n: 'separate';
    columns: number;
    [key: string]: string | number | GridItem[];
}

export type Data = TextData | FileData;
export type Document = Record<string, Data>;
