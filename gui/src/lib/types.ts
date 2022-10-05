type ElementFunc = (...args: any[]) => Element | Element[];

type FilteredKeys<ToFilter, Key> = { [K in keyof ToFilter]: ToFilter[K] extends Key ? K : never };

type DocumentsKeys = FilteredKeys<Document, ElementFunc>;

type FilteredDoc = Pick<Document, DocumentsKeys>;


export {};


