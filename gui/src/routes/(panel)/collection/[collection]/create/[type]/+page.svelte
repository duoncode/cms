<script lang="ts">
	import type { PageData } from './$types';
	import { create } from '$lib/node';
	import Node from '$shell/Node.svelte';

	type Props = {
		data: PageData;
	};

	let { data }: Props = $props();
	let collection = data.collection;
	let node = $state(data.node);

	async function save(publish: boolean) {
		if (publish) {
			node.published = true;
		}

		create(node, node.type.handle, `collection/${collection.slug}`);
	}
</script>

<Node
	bind:node
	{collection}
	{save} />
