<script lang="ts">
	import { setDirty } from '$lib/state';
	import Label from '$shell/Label.svelte';

	type Props = {
		value: string;
		label: string;
		id: string;
		required?: boolean;
	};

	let { value = $bindable(), label, id, required = false }: Props = $props();

	function oninput() {
		setDirty();
	}
</script>

<div
	class="field"
	class:required>
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
			bind:value
			{oninput} />
	</div>
</div>
