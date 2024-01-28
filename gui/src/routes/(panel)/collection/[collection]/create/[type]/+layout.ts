import type { RoutedNode, Node } from '$types/data';
import req from '$lib/req';

export const load = async ({ params, fetch, parent, route }) => {
    const collection = await parent();
    const response = await req.get(`blueprint/${params.type}`, {}, fetch);

    if (response.ok) {
        const node = response.data as Node;

        return {
            collection,
            node,
            route,
        } satisfies RoutedNode;
    }
};
