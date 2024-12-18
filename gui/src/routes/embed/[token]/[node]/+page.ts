import type { PageLoad } from './$types';
import type { Node } from '$types/data';

import { error } from '@sveltejs/kit';
import req from '$lib/req';
import { loginUserByToken } from '$lib/user';
import { currentNode, currentFields } from '$lib/state';

export const load: PageLoad = async ({ params, fetch }) => {
	const loginResult = await loginUserByToken(params.token);

	if (loginResult) {
		const response = await req.get(`node/${params.node}`, {}, fetch);

		if (response?.ok) {
			const node = response.data as Node;

			currentNode.set(node);
			currentFields.set(node.fields);

			return {
				node,
			};
		}
	} else {
		error(401, 'Unauthorized');
	}
};
