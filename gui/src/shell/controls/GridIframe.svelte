<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { GridIframe } from '$types/data';
	import type { GridField } from '$types/fields';

	type Props = {
		field: GridField;
		item: GridIframe;
		index: number;
		children: Snippet<[{ edit: () => void }]>;
	};

	let { field, item = $bindable(), index, children }: Props = $props();

	let showSettings = $state(false);
</script>

<div class="grid-cell-header">
	{@render children({ edit: () => (showSettings = !showSettings) })}
</div>
<div class="grid-cell-body">
	{#if showSettings}
		<div>Keine Einstellungsmöglichkeiten vorhanden</div>
	{:else}
		<textarea
			class="iframe"
			rows="5"
			id={`${field.name}_${index}`}
			name={`${field.name}_${index}`}
			bind:value={item.value}>
		</textarea>
	{/if}
</div>
