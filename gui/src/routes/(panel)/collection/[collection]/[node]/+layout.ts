import type { Field } from '$types/fields';
import type { Document, Node } from '$types/data';
import req from '$lib/req';
import { currentDocument, currentFields } from '$lib/state';

export const load = async ({ params, parent, route, fetch }) => {
    const collection = await parent();
    const response = await req.get(`node/${params.node}`, {}, fetch);

    if (response.ok) {
        const fields = response.data.fields as Field[];
        const doc = response.data.data as Document;

        currentDocument.set(doc);
        currentFields.set(fields);

        return {
            collection,
            type: response.data.type,
            title: response.data.title,
            uid: response.data.uid,
            fields,
            doc,
            routeId: route.id,
        } satisfies Node;
    }
};
