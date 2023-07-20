import { base } from '$app/paths';
import { get } from 'svelte/store';
import { goto } from '$app/navigation';
import { fetchCollections } from '$lib/collections';
import { loadUser, authenticated } from '$lib/user';
import { system } from '$lib/sys';
import req from '$lib/req';

let iv: null | number = null;

export const load = async ({ fetch }) => {
    await loadUser(fetch);
    const sessionExpires = get(system).sessionExpires;

    if (get(authenticated)) {
        await fetchCollections(fetch);

        if (iv !== null) {
            clearInterval(iv);
        }

        iv = setInterval(async function () {
            const resp = await req.get('me', {}, fetch);
            console.log(resp);

            if (!resp.ok) {
                clearInterval(iv);
                iv = null;
                goto(`${base}/login`);
            }
        }, 1000 * Math.floor(sessionExpires / 3.14159));
    } else {
        goto(`${base}/login`);
    }
};
