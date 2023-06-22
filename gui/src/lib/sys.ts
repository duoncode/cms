import { get } from 'svelte/store';
import req from '$lib/req';
import { writable, type Writable } from 'svelte/store';

export interface Collection {
    title: string;
    id: string;
}

export interface Type {
    name: string;
}

export interface Locale {
    id: string;
    title: string;
    fallback?: string;
}

export interface System {
    debug: boolean;
    env: string;
    csrfToken: string;
    collections: Collection[];
    types: Type[];
    locale: string;
    locales: Locale[];
    logo?: string;
}

export const system: Writable<System | null> = writable(null);

export const setup = async () => {
    const sys = get(system);

    if (sys === null) {
        const response = await req.get('boot');

        if (!response.ok) {
            throw new Error('Fatal error while requesting settings');
        }

        const data = response.data;
        const sys = {
            debug: data.debug as boolean,
            env: data.env as string,
            csrfToken: data.csrfToken as string,
            types: data.types as Type[],
            collections: data.collections as Collection[],
            locale: data.locale as string,
            locales: data.locales as Locale[],
            logo: data.logo as string,
        };

        system.set(sys);

        return sys;
    }

    return sys;
};
