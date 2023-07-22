import type { Field } from '$types/fields';

export interface User {
    uid: string;
    email: string;
    username: string;
    name: string;
    password: string;
    passwordRepeat: string;
}

export interface FileItem {
    file?: string;
    alt?: string | Record<string, string>;
    title?: string | Record<string, string>;
}

export interface TranslatedFile {
    file: string;
    alt?: string;
    title?: string;
}

export interface TextData {
    type: 'text' | 'html' | 'hidden' | 'date' | 'time' | 'datetime' | 'option';
    value?: string | Record<string, string>;
}

export interface NumberData {
    type: 'number';
    value?: number;
}

export interface FileData {
    type: 'picture' | 'image';
    files: FileItem[] | Record<string, TranslatedFile[]>;
}

export interface UploadResponse {
    ok: boolean;
    file: string;
    error: string;
}

export interface GridBase {
    type: string;
    colspan: number;
    rowspan: number;
    colstart?: number;
    width?: number; // will be added while rendering the grid
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
    value: string;
    aspectRatioX: number;
    aspectRatioY: number;
}

export type GridItem = GridHtml | GridImage | GridYoutube;

export interface LocalizedGridValue {
    [key: string]: GridItem[];
}

export interface GridData {
    type: 'grid';
    columns: number;
    value: GridItem[] | LocalizedGridValue;
}

export type Data = TextData | FileData | GridData | NumberData;
export type Content = Record<string, Data>;
export type Route = string | Record<string, string>;

export interface Column {
    value: string | boolean | number;
    bold: boolean;
    italic: boolean;
    badge: boolean;
    date: boolean;
    color: string;
}

export interface ListedNode {
    uid: string;
    columns: Column[];
}

export interface Blueprint {
    slug: string;
    name: string;
}

export interface Collection {
    name: string;
    slug: string;
    header: string[];
    nodes: ListedNode[];
    blueprints: Blueprint[];
}

export interface Document {
    uid: string;
    published: boolean;
    hidden: boolean;
    locked: boolean;
    deletable: boolean;
    created: string;
    changed: string;
    deleted: null | string;
    type: string;
    nodetype: string;
    paths: Record<string, string>;
    generatedPaths: Record<string, string>;
    route?: Route;
    content: Content;
    creator_uid: string;
    creator_email: string;
    creator_username: string;
    creator_data: {
        name: string;
    };
    editor_uid: string;
    editor_email: string;
    editor_username: string;
    editor_data: {
        name: string;
    };
}

export interface Node {
    collection: Collection;
    type: string;
    title: string;
    uid: string;
    fields: Field[];
    doc: Document;
    routeId: string;
}
