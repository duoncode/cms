import type { User } from '$types/data';
import { base } from '$app/paths';
import { writable, get } from 'svelte/store';
import { goto } from '$app/navigation';
import { _ } from '$lib/locale';
import req from './req';
import toast from '$lib/toast';

const authenticated = writable(false);
const user = writable(null);
const rememberedRoute = writable(base);

async function loginUser(login: string, password: string, rememberme: boolean) {
    const resp = await req.post('login', { login, password, rememberme });

    console.log(resp);

    if (resp.ok) {
        await loadUser(window.fetch);
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
        goto(`${base}/login`);
    } else {
        authenticated.set(true);
        throw 'Error while logging out';
    }
}

async function loadUser(fetchFn: typeof window.fetch) {
    const resp = await req.get('me', {}, fetchFn);

    if (resp.ok) {
        authenticated.set(true);
        user.set(resp.data);
    } else {
        authenticated.set(false);
        user.set(null);
    }
}

async function saveProfile(user: User) {
    const resp = await req.put('profile', user);

    if (resp.ok) {
        toast.add({
            kind: 'success',
            message: _('Benutzerprofil erfolgreich gespeichert!'),
        });
    } else {
        toast.add({
            kind: 'error',
            message: resp.data.payload.error,
        });
    }
}

export {
    loginUser,
    logoutUser,
    loadUser,
    authenticated,
    rememberedRoute,
    user,
    saveProfile,
};
