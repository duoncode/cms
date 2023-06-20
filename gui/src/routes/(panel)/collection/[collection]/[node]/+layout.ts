import type { Field } from '$types/fields';
import type { Document } from '$types/data';
import { fillMissingAttrs } from '$lib/data';
import req from '$lib/req';

export const load = async ({ params, parent }) => {
    const collection = await parent();
    const response = await req.get(`node/${params.node}`);

    if (response.ok) {
        const fields = response.data.fields as Field[];
        const data = response.data.data as Document;
        data.content = fillMissingAttrs(fields, data);

        return {
            collection,
            title: response.data.title,
            uid: response.data.uid,
            node: { fields, data },
        };
    }
    return { collection, node: {} };
};
