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

<Field {field}>
	<Label of={field.name}>
		{field.label}
	</Label>
	<div class="mt-2">
		<input
			id={field.name}
			name={field.name}
			type="text"
			required={field.required}
			bind:value={data.value} />
	</div>
</Field>
