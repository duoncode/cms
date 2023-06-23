import { base } from '$app/paths';
import { get } from 'svelte/store';
import { goto } from '$app/navigation';
import { fetchCollections } from '$lib/collections';
import { loadUser, authenticated } from '$lib/user';

export const load = async ({ fetch }) => {
    await loadUser(fetch);

    if (get(authenticated)) {
        await fetchCollections(fetch);
    } else {
        goto(`${base}/login`);
    }
};
