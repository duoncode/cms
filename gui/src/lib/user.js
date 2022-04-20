import { writable, get } from 'svelte/store';
import { replace } from "svelte-spa-router";
import req from './req';

const authenticated = writable(false);
const user = writable(null);
const rememberedRoute = writable('/');


async function loginUser(login, password, rememberme) {
    let resp = await req.post('/login', { login, password, rememberme });

    if (resp.ok) {
        await loadUser();
        replace(get(rememberedRoute));

        return true;
    } else {
        return resp.data.error;
    }
}

async function logoutUser() {
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
        logoutUser();
    }
}

export {
    loginUser,
    logoutUser,
    loadUser,
    authenticated,
    rememberedRoute,
    user,
};
