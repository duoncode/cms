<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { GridYoutube } from '$types/data';
	import type { GridField } from '$types/fields';

	import { setDirty } from '$lib/state';
	import { _ } from '$lib/locale';
	import Setting from '$shell/Setting.svelte';

	type Props = {
		field: GridField;
		item: GridYoutube;
		index: number;
		children: Snippet<[{ edit: () => void }]>;
	};

	let { field, item = $bindable(), index, children }: Props = $props();

	let showSettings = $state(false);
	let x = $derived(item.aspectRatioX ? item.aspectRatioX : 16);
	let y = $derived(item.aspectRatioY ? item.aspectRatioY : 9);
	let percent = $derived(parseFloat(((y / x) * 100).toFixed(2)));

	if (!item.value) {
		showSettings = true;
	}

	function oninput() {
		setDirty();
	}
</script>

<div class="grid-cell-header">
	{@render children({ edit: () => (showSettings = !showSettings) })}
</div>
<div class="grid-cell-body">
	{#if showSettings}
		<Setting>
			<label for={field.name + '_' + index + '_ytid'}>
				{_('Youtube-ID')}
			</label>
			<div class="mt-2">
				<input
					id={field.name + '_' + index + '_ytid'}
					name={field.name + '_' + index + '_ytid'}
					type="text"
					maxlength="20"
					placeholder={_('Fügen Sie hier die Youtube-ID ein')}
					bind:value={item.value}
					{oninput} />
			</div>
		</Setting>
		<Setting>
			<label for={field.name + '_' + index + '_x'}>
				{_('Seitenverhältnis')}
			</label>
			<div class="mt-2 flex flex-row gap-4">
				<input
					id={field.name + '_' + index + '_x'}
					name={field.name + '_' + index + '_x'}
					type="number"
					max="100"
					min="1"
					placeholder={_('Breite')}
					bind:value={item.aspectRatioX}
					{oninput} />
				<input
					id={field.name + '_' + index + '_y'}
					name={field.name + '_' + index + '_y'}
					type="number"
					max="100"
					min="1"
					placeholder={_('Höhe')}
					bind:value={item.aspectRatioY}
					{oninput} />
			</div>
		</Setting>
	{:else}
		<div class="youtube-container">
			<div
				class="relative"
				style="padding-top: {percent}%">
				<iframe
					class="youtube absolute left-0 top-0 h-full w-full"
					title="Youtube Video"
					src="https://www.youtube.com/embed/{item.value}"
					allowfullscreen>
				</iframe>
			</div>
		</div>
	{/if}
</div>
