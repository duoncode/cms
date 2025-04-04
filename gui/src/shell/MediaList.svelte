<script lang="ts">
	import type { FileItem, UploadType } from '$types/data';
	import type { ModalFunctions } from '$shell/modal';
	import type { SortableEvent } from 'sortablejs';
	import { getContext } from 'svelte';
	import Sortable from 'sortablejs';
	import { onMount } from 'svelte';
	import Image from '$shell/Image.svelte';
	import Video from '$shell/Video.svelte';
	import File from '$shell/File.svelte';
	import ModalEditImage from '$shell/modals/ModalEditImage.svelte';

	type Props = {
		assets: FileItem[];
		multiple: boolean;
		translate: boolean;
		type: UploadType;
		loading: boolean;
		path: string;
		remove: (index: number) => void;
	};

	let {
		assets = $bindable(),
		multiple,
		translate,
		type,
		loading,
		path,
		remove,
	}: Props = $props();
	let { open, close } = getContext<ModalFunctions>('modal');
	let sorterElement: HTMLElement = $state();

	function createSorter() {
		if (sorterElement) {
			Sortable.create(sorterElement, {
				animation: 200,
				onUpdate: function (event: SortableEvent) {
					const tmp = assets[event.oldIndex];

					assets.splice(event.oldIndex, 1);
					assets.splice(event.newIndex, 0, tmp);
				},
			});
		}
	}

	function edit(index: number, hasAlt: boolean) {
		const apply = (asset: FileItem) => {
			assets[index] = asset;
			close();
		};

		open(
			ModalEditImage,
			{
				asset: assets[index],
				close,
				apply,
				translate,
				hasAlt,
			},
			{},
		);
	}

	onMount(createSorter);
</script>

{#if multiple && type === 'image'}
	<div
		class="multiple-images"
		bind:this={sorterElement}>
		{#each assets as asset, index (asset)}
			<Image
				upload
				{multiple}
				{path}
				image={asset}
				remove={() => remove(index)}
				edit={() => edit(index, true)}
				{loading} />
		{/each}
	</div>
{:else if !multiple && type === 'image' && assets && assets.length > 0}
	<Image
		upload
		{path}
		{multiple}
		image={assets[0]}
		remove={() => remove(null)}
		edit={() => edit(0, true)}
		{loading} />
{:else if multiple && type === 'file'}
	<div
		class="multiple-files mb-3 flex flex-col gap-3"
		bind:this={sorterElement}>
		{#each assets as asset, index (asset)}
			<File
				{path}
				{loading}
				{asset}
				remove={() => remove(index)}
				edit={() => edit(index, false)} />
		{/each}
	</div>
{:else if !multiple && type === 'video' && assets && assets.length > 0}
	<Video
		upload
		{path}
		file={assets[0]}
		remove={() => remove(null)}
		edit={() => edit(0, true)}
		{loading} />
{:else if assets && assets.length > 0}
	<File
		{path}
		{loading}
		asset={assets[0]}
		remove={() => remove(null)}
		edit={() => edit(0, false)} />
{/if}

<style lang="postcss">
	.multiple-images {
		@apply flex flex-row flex-wrap justify-start gap-4 py-4;
	}
</style>
