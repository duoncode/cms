import type { PageLoad } from './$types';
import type { Node } from '$types/data';

import { error } from '@sveltejs/kit';
import req from '$lib/req';
import qs from '$lib/qs';
import { loginUserByToken } from '$lib/user';
import { currentNode, currentFields } from '$lib/state';

export const load: PageLoad = async ({ params, fetch, url }) => {
	const fields = qs.asArray(url, 'fields');

	if (!(await loginUserByToken(params.token))) {
		error(401, 'Unauthorized');
	}

	const response = await req.get(`node/${params.node}`, {}, fetch);

	if (response?.ok) {
		const node = response.data as Node;

		currentNode.set(node);
		currentFields.set(node.fields);

		return {
			node,
			fields,
		};
	}

	error(404, 'Not Found');
};
