<script lang="ts">
	import Field from '$shell/Field.svelte';
	import Label from '$shell/Label.svelte';
	import type { TextData } from '$types/data';
	import type { SimpleField } from '$types/fields';

	type Props = {
		field: SimpleField;
		data: TextData;
	};

	let { field, data = $bindable() }: Props = $props();
</script>

<Field required={field.required}>
	<Label of={field.name}>
		{field.label}
	</Label>
	<div class="mt-2">
		<select
			id={field.name}
			name={field.name}
			required={field.required}
			bind:value={data.value}>
			{#each field.options as option}
				{#if typeof option === 'object'}
					<option value={option.value}>{option.label}</option>
				{:else}
					<option value={option}>{option}</option>
				{/if}
			{/each}
		</select>
	</div>
</Field>
