import type { PageLoad } from './$types';
import type { Node } from '$types/data';

import { error } from '@sveltejs/kit';
import req from '$lib/req';
import { loginUserByToken } from '$lib/user';
import { currentNode, currentFields } from '$lib/state';

export const load: PageLoad = async ({ params, fetch, url }) => {
	if (!(await loginUserByToken(params.token))) {
		error(401, 'Unauthorized');
	}

	let uri = `blueprint/${params.type}`;
	const defaults = url.searchParams.get('content');

	if (defaults) {
		uri = `${uri}?content=${encodeURI(defaults)}`;
	}

	const response = await req.get(uri, {}, fetch);

	if (response?.ok) {
		const node = response.data as Node;

		currentNode.set(node);
		currentFields.set(node.fields);

		return {
			type: params.type,
			token: params.token,
			node,
		};
	}

	error(404, 'Not Found');
};
