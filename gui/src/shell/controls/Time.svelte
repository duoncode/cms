<script lang="ts">
	import type { TextData } from '$types/data';
	import type { SimpleField } from '$types/fields';

	import { setDirty } from '$lib/state';
	import Field from '$shell/Field.svelte';
	import Label from '$shell/Label.svelte';

	type Props = {
		field: SimpleField;
		data: TextData;
	};

	let { field, data = $bindable() }: Props = $props();

	function oninput() {
		setDirty();
	}
</script>

<Field {field}>
	<Label of={field.name}>
		{field.label}
	</Label>
	<div class="mt-2">
		<input
			id={field.name}
			name={field.name}
			type="time"
			required={field.required}
			disabled={field.immutable}
			bind:value={data.value}
			{oninput} />
	</div>
</Field>
