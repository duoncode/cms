import type { User } from '$types/data';
import req from '$lib/req';

export const load = async ({ fetch }) => {
    const response = await req.get(`profile`, {}, fetch);

    if (response.ok) {
        const data = response.data;

        return {
            user: {
                uid: data.uid,
                email: data.email,
                username: data.username,
                name: data.name,
                password: '',
                passwordRepeat: '',
            } satisfies User,
        };
    }
};
