import type { LayoutLoad } from './$types';

import '../styles/main.css';
import { setup } from '$lib/sys';

export const ssr = false;

export const load: LayoutLoad = async ({ fetch }) => {
	const system = await setup(fetch);

	return { system };
};
