import type { LayoutLoad } from './$types';

import { get } from 'svelte/store';
import { goto } from '$app/navigation';
import { fetchCollections } from '$lib/collections';
import { loadUser } from '$lib/user';
import { system } from '$lib/sys';
import { base } from '$lib/req';
import req from '$lib/req';

let iv: null | number = null;

export const load: LayoutLoad = async ({ fetch }) => {
	const authenticated = await loadUser(fetch);
	const sessionExpires = get(system).sessionExpires;

	if (authenticated) {
		await fetchCollections(fetch);

		if (iv !== null) {
			clearInterval(iv);
		}

		iv = setInterval(
			async function () {
				const resp = await req.get('me', {}, fetch);

				if (!resp?.ok) {
					if (iv !== null) {
						clearInterval(iv);
					}

					iv = null;
					goto(`${base()}login`);
				}
			},
			1000 * Math.floor(sessionExpires / 3.14159),
		);
	} else {
		goto(`${base()}login`);
	}
};
