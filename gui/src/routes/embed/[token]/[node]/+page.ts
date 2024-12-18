import type { PageLoad } from './$types';
import type { Node } from '$types/data';

import { error } from '@sveltejs/kit';
import req from '$lib/req';
import { loadUser, loginUserByToken } from '$lib/user';
import { currentNode, currentFields } from '$lib/state';

export const load: PageLoad = async ({ params, fetch }) => {
	const authenticated = await loadUser(fetch);
	const token = params.token;

	if (authenticated) {
		req.post('invalidate-token', { token }, fetch);
	} else {
		const loginResult = await loginUserByToken(token);

		if (!loginResult) {
			error(401, 'Unauthorized');
		}
	}

	const response = await req.get(`node/${params.node}`, {}, fetch);

	if (response?.ok) {
		const node = response.data as Node;

		currentNode.set(node);
		currentFields.set(node.fields);

		return {
			node,
		};
	}

	error(404, 'Not Found');
};
