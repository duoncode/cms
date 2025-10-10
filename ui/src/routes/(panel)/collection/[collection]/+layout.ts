import req from '$lib/req';
import type { Collection } from '$types/data';
import { currentNode, currentFields } from '$lib/state';

export const load = async ({ params, fetch }) => {
	currentNode.set(null);
	currentFields.set(null);

	const response = await req.get(`collection/${params.collection}`, {}, fetch);
	return (response.ok ? response.data : {}) as Collection;
};
