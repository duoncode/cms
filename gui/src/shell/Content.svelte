<script lang="ts">
	import type { Node } from '$types/data';
	import type { Field } from '$types/fields';
	import controls from '$lib/controls';

	type Props = {
		node: Node;
		fields: Field[];
	};

	let { node = $bindable(), fields = $bindable() }: Props = $props();

	function fieldSpan(value: number | null, type: 'row' | 'col') {
		if (value) {
			if (value > 100 || value <= 0) value = 100;

			return `span ${value} / span ${value}`;
		}

		if (type === 'col') {
			return 'span 100 / span 100';
		}

		return 'span 1 / span 1';
	}
</script>

<div class="field-grid hans">
	{#each fields as field}
		{#if !field.hidden}
			<div
				style="
					grid-column: {fieldSpan(field.width, 'col')};
					grid-row: {fieldSpan(field.rows, 'row')}">
				{#if controls[field.type]}
					{@const SvelteComponent = controls[field.type]}
					<SvelteComponent
						{field}
						node={node.uid}
						bind:data={node.content[field.name]} />
				{:else}
					{field.type}
				{/if}
			</div>
		{/if}
	{/each}
</div>

<style lang="postcss">
	.field-grid {
		display: grid;
		grid-template-columns: repeat(100, minmax(0, 1fr));
	}
</style>
