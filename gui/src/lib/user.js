import { writable } from 'svelte/store';

const authenticated = writable(false);

function get() {
}

export {
    authenticated,
    get,
};

