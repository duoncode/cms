import { writable } from 'svelte/store';

const navVisible = writable(true);

function closeNav() {
    navVisible.set(false);
}

function openNav() {
    navVisible.set(true);
}

function toggleNav() {
    navVisible.update(state => !state);
}

export {
    openNav,
    closeNav,
    toggleNav,
    navVisible,
};

