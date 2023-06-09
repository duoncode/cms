import { writable, get } from 'svelte/store';
import { replace } from 'svelte-spa-router';
import req from './req';

const authenticated = writable(false);
const user = writable(null);
const rememberedRoute = writable('/');

async function loginUser(login, password, rememberme) {
    let resp = await req.post('api/login', { login, password, rememberme });

    if (resp.ok) {
        await loadUser();
        replace(get(rememberedRoute));

        return true;
    } else {
        return resp.data.error;
    }
}

async function logoutUser() {
    authenticated.set(false);
    let resp = await req.post('logout');

    if (resp.ok) {
        user.set(null);
        replace('/login');
    } else {
        authenticated.set(true);
        throw 'Error while logging out';
    }
}

async function loadUser() {
    let resp = await req.get('me');

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
