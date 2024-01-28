import type { Field } from '$types/fields';
import type { RoutedNode, Node } from '$types/data';
import req from '$lib/req';
import { currentNode, currentFields } from '$lib/state';

export const load = async ({ params, parent, route, fetch }) => {
    const collection = await parent();
    const response = await req.get(`node/${params.node}`, {}, fetch);

    if (response.ok) {
        const node = response.data as Node;

        currentNode.set(node);
        currentFields.set(node.fields);

        return {
            collection,
            route,
            node,
        } satisfies RoutedNode;
    }
};
