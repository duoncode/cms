<script lang="ts">
	import { setDirty } from '$lib/state';
	import Field from '$shell/Field.svelte';
	import type { BooleanData } from '$types/data';
	import type { SimpleField } from '$types/fields';

	type Props = {
		field: SimpleField;
		data: BooleanData;
	};

	let { field, data = $bindable() }: Props = $props();

	function onchange() {
		setDirty();
	}
</script>

<Field {field}>
	<div class="relative flex items-start">
		<div class="flex h-6 items-center">
			<input
				id={field.name}
				name={field.name}
				type="checkbox"
				class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-600"
				disabled={field.immutable}
				bind:checked={data.value}
				{onchange} />
		</div>
		<div class="ml-3 text-sm leading-6">
			<label
				for={field.name}
				class="font-medium text-gray-900">
				{field.label}
			</label>
			{#if field.description}
				<p class="text-gray-500">{field.description}</p>
			{/if}
		</div>
	</div>
</Field>

<style lang="postcss">
	input[type='checkbox'] {
		border-width: 1.5px;
	}
</style>
