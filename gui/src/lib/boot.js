import { writable, get } from 'svelte/store';
import req from './req';

let settings = writable({});


async function loadSettings() {
    let response = await req.get('/settings');

    if (response.ok) {
        settings = response.data;
    } else {
        throw new Error('Fatal error while requesting settings');
    }
}

function getSettings() {
    return get(settings);
}

export {
    loadSettings,
    getSettings,
    settings,
}
