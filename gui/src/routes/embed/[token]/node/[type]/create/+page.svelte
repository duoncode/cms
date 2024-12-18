<script lang="ts">
	import type { PageData } from './$types';
	import EmbeddedNode from '$shell/EmbeddedNode.svelte';
	import { create } from '$lib/node';

	type Props = {
		data: PageData;
	};

	let { data }: Props = $props();
	let node = $state(data.node);

	async function save(publish: boolean) {
		if (publish) {
			node.published = true;
		}

		create(node, node.type.handle, `embed/${data.token}/node/${data.type}`);
	}
</script>

<EmbeddedNode
	bind:node
	{save} />
