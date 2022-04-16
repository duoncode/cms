import { writable, get } from 'svelte/store';
import req from './req';

let settings = writable({});


async function loadSettings() {
    let response = await req.get('/settings');

    settings = response.json();
}

function getSettings() {
    return get(settings);
}

export {
    loadSettings,
    getSettings,
    settings,
}
