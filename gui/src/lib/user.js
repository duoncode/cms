import { writable } from 'svelte/store';
import req from './req';

const authenticated = writable(false);
const user = writable(null);

async function loadUser() {
    let user = await req.get('/currentuser');

    if (user.ok) {
        authenticated.set(true);
        user.set(user.data);
    }
}

export {
    loadUser,
    authenticated,
    user,
};
