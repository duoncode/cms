<script lang="ts">
	import { system } from '$lib/sys';
	import Field from '$shell/Field.svelte';
	import Upload from '$shell/Upload.svelte';
	import LabelDiv from '$shell/LabelDiv.svelte';
	import type { FileData } from '$types/data';
	import type { ImageField } from '$types/fields';

	type Props = {
		field: ImageField;
		data: FileData;
		node: string;
		type: 'image' | 'file';
	};

	let { field, data = $bindable(), node, type }: Props = $props();

	let lang = $state($system.locale);
</script>

<Field
	{field}
	class="flex h-full flex-col">
	<LabelDiv
		translate={field.translate}
		bind:lang>
		{field.label}
	</LabelDiv>
	<div class="mt-2 flex-grow">
		{#if field.translateFile}
			{#each $system.locales as locale}
				{#if locale.id === lang}
					<Upload
						{type}
						multiple={field.multiple}
						path="{$system.prefix}/media/{type}/node/{node}"
						required={field.required}
						name={field.name}
						translate={false}
						bind:assets={data.files[locale.id]} />
				{/if}
			{/each}
		{:else}
			<Upload
				{type}
				multiple={field.multiple}
				path="{$system.prefix}/media/{type}/node/{node}"
				required={field.required}
				name={field.name}
				translate={field.translate}
				bind:assets={data.files} />
		{/if}
	</div>
</Field>
