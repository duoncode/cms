<script lang="ts">
	import Field from '$shell/Field.svelte';
	import Label from '$shell/Label.svelte';

	type Props = {
		value: string;
		label: string;
		id: string;
		required?: boolean;
	};

	let { value = $bindable(), label, id, required = false }: Props = $props();
</script>

<Field {required}>
	<Label of={id}>
		{label}
	</Label>
	<div class="mt-2">
		<input
			{id}
			name={id}
			type="password"
			{required}
			autocomplete="new-password"
			bind:value />
	</div>
</Field>
