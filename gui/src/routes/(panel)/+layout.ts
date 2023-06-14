import { get } from 'svelte/store';
import { goto } from '$app/navigation';
import { setup } from '$lib/sys';
import { authenticated } from '$lib/user';

export const ssr = false;

export const load = async () => {
    if (get(authenticated)) {
        const system = await setup();

        return { system };
    } else {
        goto('/login');
    }
};
