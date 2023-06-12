import { writable, get } from 'svelte/store';
import { replace } from 'svelte-spa-router';
import req from './req';

const authenticated = writable(false);
const user = writable(null);
const rememberedRoute = writable('/');

async function loginUser(login, password, rememberme) {
    const resp = await req.post('api/login', { login, password, rememberme });

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
    const resp = await req.post('logout');

    if (resp.ok) {
        user.set(null);
        replace('/login');
    } else {
        authenticated.set(true);
        throw 'Error while logging out';
    }
}

async function loadUser() {
    const resp = await req.get('me');

    if (resp.ok) {
        authenticated.set(true);
        user.set(resp.data);
    } else {
        user.set(null);
        replace('/login');
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
