<script lang="ts">
	import type { PageData } from './$types';
	import type { Node } from '$types/data';
	import req from '$lib/req';
	import { save as saveNode } from '$lib/node';
	import EmbeddedNode from '$shell/EmbeddedNode.svelte';

	type Props = {
		data: PageData;
	};

	let { data }: Props = $props();
	let node = $state(data.node);

	async function save(publish: boolean) {
		if (publish) {
			node.published = true;
		}

		const result = await saveNode(node.uid, node);

		if (result.success) {
			const response = await req.get(`node/${result.uid}`);

			if (response?.ok) {
				node = response.data as Node;
			}
		}
	}
</script>

<EmbeddedNode
	bind:node
	{save} />
