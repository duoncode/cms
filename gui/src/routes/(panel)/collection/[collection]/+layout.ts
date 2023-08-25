import req from '$lib/req';
import type { Collection } from '$types/data';
import { currentDocument, currentFields } from '$lib/state';

export const load = async ({ params, fetch }) => {
    currentDocument.set(null);
    currentFields.set(null);

    const response = await req.get(`collection/${params.collection}`, {}, fetch);
    return (response.ok ? response.data : {}) as Collection;
};
