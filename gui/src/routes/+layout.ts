import '../styles/main.css';
import { setup } from '$lib/sys';

export const ssr = false;

export const load = async ({ fetch }) => {
    const system = await setup(fetch);

    return { system };
};
