import type { Field } from '$types/fields';
import type { Document, Node } from '$types/data';
import { fillMissingAttrs } from '$lib/data';
import req from '$lib/req';

export const load = async ({ params, parent, route }) => {
    const collection = await parent();
    const response = await req.get(`node/${params.node}`);

    if (response.ok) {
        const fields = response.data.fields as Field[];
        const doc = response.data.data as Document;
        doc.content = fillMissingAttrs(fields, doc);

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
