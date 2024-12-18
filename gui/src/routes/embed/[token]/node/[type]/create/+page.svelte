<script lang="ts">
	import type { PageData } from './$types';
	import EmbeddedNode from '$shell/EmbeddedNode.svelte';
	import { create } from '$lib/node';

	type Props = {
		data: PageData;
	};

	let { data }: Props = $props();

	async function save(publish: boolean) {
		if (publish) {
			data.node.published = true;
		}

		create(data.node, data.node.type.handle, `embed/${data.token}/node/${data.type}`);
	}
</script>

<EmbeddedNode
	bind:node={data.node}
	{save} />
