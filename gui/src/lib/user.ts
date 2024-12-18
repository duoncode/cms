import type { User } from '$types/data';
import { base } from '$app/paths';
import { writable, get } from 'svelte/store';
import { goto } from '$app/navigation';
import { _ } from '$lib/locale';
import req from './req';
import toast from '$lib/toast';

export const authenticated = writable(false);
export const user = writable(null);
export const rememberedRoute = writable(base);

export async function loginUser(login: string, password: string, rememberme: boolean) {
	const resp = await req.post('login', { login, password, rememberme });

	if (resp?.ok) {
		await loadUser(window.fetch);
		goto(get(rememberedRoute));

		return true;
	} else {
		return resp?.data.error;
	}
}

export async function loginUserByToken(token: string) {
	const resp = await req.post('token-login', { token });

	if (resp?.ok) {
		await loadUser(window.fetch);

		return true;
	} else {
		return false;
	}
}

export async function logoutUser() {
	authenticated.set(false);
	const resp = await req.post('logout');

	if (resp?.ok) {
		user.set(null);
		goto(`${base}/login`);
	} else {
		authenticated.set(true);
		throw 'Error while logging out';
	}
}

export async function loadUser(fetchFn: typeof window.fetch) {
	const resp = await req.get('me', {}, fetchFn);

	if (resp?.ok) {
		authenticated.set(true);
		user.set(resp.data);

		return true;
	}
	authenticated.set(false);
	user.set(null);

	return false;
}

export async function saveProfile(user: User) {
	const resp = await req.put('profile', user);

	if (resp?.ok) {
		toast.add({
			kind: 'success',
			message: _('Benutzerprofil erfolgreich gespeichert!'),
		});
	} else {
		toast.add({
			kind: 'error',
			message: resp?.data.payload.error,
		});
	}
}
