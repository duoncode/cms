import req from './req';
import { writable, type Writable } from 'svelte/store';

export interface Collection {
    title: string;
    id: string;
}

export interface Type {
    name: string;
}

export class System {
    debug: boolean;
    env: string;
    panelPath: string;
    csrfToken: string;
    collections: Collection[];
    types: Type[];

    async boot() {
        const response = await req.get('boot');

        if (response.ok) {
            const data = response.data;

            this.debug = data.debug as boolean;
            this.env = data.env as string;
            this.panelPath = data.panelPath as string;
            this.csrfToken = data.csrfToken as string;
            this.types = data.types as Type[];
            this.collections = data.collections as Collection[];
        } else {
            throw new Error('Fatal error while requesting settings');
        }
    }
}

export const system: Writable<System | null> = writable(null);

export const setup = async () => {
    const sys = new System();

    await sys.boot();
    system.set(sys);

    return sys;
};
