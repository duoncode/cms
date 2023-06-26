import type { Field } from '$types/fields';
import type { Document, Node } from '$types/data';
import req from '$lib/req';

export const load = async ({ params, fetch, parent, route }) => {
    const collection = await parent();
    const response = await req.get(`blueprint/${params.slug}`, {}, fetch);

    if (response.ok) {
        const fields = response.data.fields as Field[];
        const doc = response.data.data as Document;

        return {
            collection,
            title: response.data.title,
            uid: response.data.uid,
            fields,
            doc,
            routeId: route.id,
        } satisfies Node;
    }
};
