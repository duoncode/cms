export interface File {
    file: string;
    alt: string | Record<string, string>;
    title: string | Record<string, string>;
}

export interface TranslatedFile {
    file: string;
    alt: string;
    title: string;
}

export interface TextData {
    type: 'text' | 'html' | 'hidden' | 'date' | 'time';
    value: string | Record<string, string>;
}

export interface ImageData {
    type: 'picture' | 'image';
    files: File[] | Record<string, TranslatedFile>;
}

export type Data = TextData | ImageData;
export type Document = Record<string, Data>;
