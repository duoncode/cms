interface File {
    file: string;
    alt: string | Record<string, string>;
}

interface TranslatedFile {
    file: string;
    alt: string;
}

export interface TextData {
    type: 'text';
    value: string | Record<string, string>;
}

export interface ImageData {
    type: 'picture' | 'image';
    files: File[] | Record<string, TranslatedFile>[];
}
