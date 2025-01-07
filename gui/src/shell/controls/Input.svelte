<script lang="ts">
	import Label from '$shell/Label.svelte';
	import { system } from '$lib/sys';

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

	let lang = $state($system.locale);
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
						bind:value={value[locale.id]} />
				{/if}
			{/each}
		{:else}
			<input
				{id}
				name={id}
				type="text"
				{required}
				autocomplete="off"
				bind:value />
		{/if}
	</div>
	{#if description}
		<div class="mt-1 text-sm text-gray-400">
			{description}
		</div>
	{/if}
</div>
