import req from '$lib/req';
import type { Node } from '$types/data';

export const load = async ({ params, fetch }) => {
    const response = await req.get(`blueprint/${params.slug}`, {}, fetch);
    return (response.ok ? response.data : {}) as Node;
};
