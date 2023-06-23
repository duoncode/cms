import { writable, type Writable } from 'svelte/store';
import req from '$lib/req';

export interface Collection {
    slug: string;
    title: string;
}

export const collections: Writable<Collection[]> = writable([]);

export async function fetchCollections(fetchFn: typeof window.fetch) {
    const response = await req.get('collections', {}, fetchFn);

    if (!response.ok) {
        throw new Error('Fatal error while requesting collections');
    }

    const data = response.data as Collection[];

    collections.set(data);

    return data;
}
