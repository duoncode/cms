<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { GridImage } from '$types/data';
	import type { GridField } from '$types/fields';

	import Upload from '$shell/Upload.svelte';
	import { system } from '$lib/sys';

	type Props = {
		field: GridField;
		item: GridImage;
		node: string;
		index: number;
		children: Snippet<[{ edit: () => void }]>;
	};

	let { field, item = $bindable(), node, index, children }: Props = $props();

	let showSettings = $state(false);
</script>

<div class="grid-cell-header">
	{@render children({ edit: () => (showSettings = !showSettings) })}
</div>
<div class="grid-cell-body">
	{#if showSettings}
		<div>Keine Einstellungsmöglichkeiten vorhanden</div>
	{:else}
		<Upload
			type="image"
			multiple={true}
			path="{$system.prefix}/media/image/node/{node}"
			name={field.name + '_' + index}
			translate={false}
			bind:assets={item.files} />
	{/if}
</div>
