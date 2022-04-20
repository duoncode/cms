import { writable } from 'svelte/store';
import { replace } from "svelte-spa-router";
import req from './req';

const authenticated = writable(false);
const user = writable(null);


async function logout() {
    let resp = await req.post('/logout');

    if (resp.ok) {
        authenticated.set(false);
        user.set(null);
        replace('/login');
    } else {
        throw 'Error while logging out';
    }
}

async function loadUser() {
    let resp = await req.get('/currentuser');

    if (resp.ok) {
        authenticated.set(true);
        user.set(resp.data);
    } else {
        logout();
    }
}

export {
    loadUser,
    logout,
    authenticated,
    user,
};
