import '../styles/main.css';
import { setup } from '$lib/sys';

export const ssr = false;

export const load = async () => {
    const system = await setup();

    return { system };
};
