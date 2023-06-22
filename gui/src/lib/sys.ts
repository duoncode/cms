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
    initialized: boolean;
    debug: boolean;
    env: string;
    csrfToken: string;
    collections: Collection[];
    locale: string;
    locales: Locale[];
    logo?: string;
}

export const system: Writable<System> = writable({
    initialized: false,
    debug: false,
    env: 'production',
    csrfToken: '',
    collections: [],
    locale: 'en',
    locales: [],
});

export const setup = async (fetchFn: typeof window.fetch) => {
    const sys = get(system);

    if (!sys.initialized) {
        const response = await req.get('boot', {}, fetchFn);

        if (!response.ok) {
            throw new Error('Fatal error while requesting settings');
        }

        const data = response.data;
        const sys = {
            initialized: true,
            debug: data.debug as boolean,
            env: data.env as string,
            csrfToken: data.csrfToken as string,
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
