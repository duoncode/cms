<script lang="ts">
	import type { TextData } from '$types/data';
	import type { SimpleField } from '$types/fields';

	import { system } from '$lib/sys';
	import { setDirty } from '$lib/state';
	import Field from '$shell/Field.svelte';
	import Label from '$shell/Label.svelte';

	type Props = {
		field: SimpleField;
		data: TextData;
	};

	let { field, data = $bindable() }: Props = $props();

	let lang = $state($system.locale);

	function oninput() {
		setDirty();
	}
</script>

<Field {field}>
	<Label
		of={field.name}
		translate={field.translate}
		bind:lang>
		{field.label}
	</Label>
	<div class="mt-2">
		{#if field.translate}
			{#each $system.locales as locale}
				{#if locale.id === lang}
					<input
						id={field.name}
						name={field.name}
						type="text"
						required={field.required}
						disabled={field.immutable}
						bind:value={data.value[locale.id]}
						{oninput} />
				{/if}
			{/each}
		{:else}
			<input
				id={field.name}
				name={field.name}
				type="text"
				required={field.required}
				disabled={field.immutable}
				bind:value={data.value}
				{oninput} />
		{/if}
	</div>
</Field>
