import { writable, get } from 'svelte/store';
import { goto } from '$app/navigation';
import req from './req';

const authenticated = writable(false);
const user = writable(null);
const rememberedRoute = writable('/');

async function loginUser(login: string, password: string, rememberme: boolean) {
    const resp = await req.post('login', { login, password, rememberme });

    if (resp.ok) {
        await loadUser();
        goto(get(rememberedRoute));

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
        goto('/login');
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
        goto('/login');
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
