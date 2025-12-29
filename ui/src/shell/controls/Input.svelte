<script lang="ts">
	import { system, systemLocale } from '$lib/sys';
	import { setDirty } from '$lib/state';
	import Label from '$shell/Label.svelte';

	type Props = {
		value: string | Record<string, string>;
		label: string;
		id: string;
		required?: boolean;
		translate?: boolean;
		description?: string;
	};

	let {
		value = $bindable(),
		label,
		id,
		required = false,
		translate = false,
		description = '',
	}: Props = $props();

	let lang = $state(systemLocale($system));

	function oninput() {
		setDirty();
	}
</script>

<div
	class="field"
	class:required>
	<Label
		of={id}
		{translate}
		bind:lang>
		{label}
	</Label>
	<div class="mt-2">
		{#if translate}
			{#each $system.locales as locale}
				{#if locale.id === lang}
					<input
						{id}
						name={id}
						type="text"
						{required}
						autocomplete="off"
						bind:value={value[locale.id]}
						{oninput} />
				{/if}
			{/each}
		{:else}
			<input
				{id}
				name={id}
				type="text"
				{required}
				autocomplete="off"
				bind:value
				{oninput} />
		{/if}
	</div>
	{#if description}
		<div class="mt-1 text-sm text-gray-400">
			{description}
		</div>
	{/if}
</div>
