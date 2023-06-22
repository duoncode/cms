import { get } from 'svelte/store';
import { goto } from '$app/navigation';
import { loadUser, authenticated } from '$lib/user';

export const load = async ({ fetch }) => {
    await loadUser(fetch);

    if (!get(authenticated)) {
        goto('/panel/login');
    }
};
