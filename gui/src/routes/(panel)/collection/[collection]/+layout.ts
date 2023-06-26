import req from '$lib/req';
import type { Collection } from '$types/data';

export const load = async ({ params, fetch }) => {
    const response = await req.get(
        `collection/${params.collection}`,
        {},
        fetch,
    );
    return (response.ok ? response.data : {}) as Collection;
};
