<script lang="ts">
	import type { FileItem } from '$types/data';
	import type { ModalFunctions } from '$shell/modal';

	import { getContext } from 'svelte';
	import { system } from '$lib/sys';
	import { _ } from '$lib/locale';
	import IcoTrash from '$shell/icons/IcoTrash.svelte';
	import IcoEye from '$shell/icons/IcoEye.svelte';
	import IcoPencil from '$shell/icons/IcoPencil.svelte';
	import ImagePreview from '$shell/ImagePreview.svelte';

	type Props = {
		path: string;
		image: FileItem;
		loading: boolean;
		upload: boolean;
		multiple: boolean;
		remove: () => void;
		edit: () => void;
		class?: string;
	};

	let {
		path,
		image,
		loading,
		upload,
		multiple,
		remove,
		edit,
		class: classes = '',
	}: Props = $props();

	let { open, close } = getContext<ModalFunctions>('modal');

	let hover = $state(false);
	let ext = $derived(image.file.split('.').pop()?.toLowerCase());
	let orig = $derived(`${path}/${image.file}`);
	let thumb = $derived(ext === 'svg' ? orig : `${path}/${thumbIt(image.file)}`);
	let title = $derived(getTitle(image, 'title') || getTitle(image, 'alt'));

	function preview() {
		open(
			ImagePreview,
			{
				close,
				image: orig,
			},
			{},
		);
	}

	function thumbIt(image: string) {
		return image + '?resize=width&w=400';
	}

	function getTitle(image: FileItem, key: string) {
		if (image[key]) {
			if (typeof image[key] === 'string') {
				return image[key];
			}

			for (const locale of $system.locales) {
				if (image[key][locale.id]) {
					return image[key][locale.id];
				}
			}
		}

		return '';
	}
</script>

<div
	class="image relative border border-gray-300 bg-gray-100 p-1 text-center {classes}"
	class:empty={!image}
	class:upload
	class:multiple
	class:hover>
	{#if loading}
		{_('Loading ...')}
	{:else}
		<img
			src={thumb}
			alt={_('Vorschau')} />
		<div
			class="overlay invisible absolute top-1 right-1 bottom-1 left-1 flex flex-row items-center justify-center gap-2 opacity-0">
			{#if remove}
				<button
					class="text-rose-700"
					onclick={remove}>
					<span class="ico">
						<IcoTrash />
					</span>
					<span class="icobtn">{_('Löschen')}</span>
				</button>
			{/if}
			<button
				class="text-sky-700"
				onclick={preview}>
				<span class="ico">
					<IcoEye />
				</span>
				<span class="icobtn">{_('Vorschau')}</span>
			</button>
			<button
				class="text-sky-700"
				onclick={edit}>
				<span class="ico">
					<IcoPencil />
				</span>
				<span class="icobtn">{_('Titel')}</span>
			</button>
		</div>
	{/if}
	{#if title}
		<button
			class="title absolute bottom-1 left-1 mb-px ml-px truncate rounded bg-white px-1 text-xs text-gray-600"
			onclick={edit}>
			{title}
		</button>
	{/if}
	{#if ext}
		<span
			class="absolute right-1 bottom-1 mr-px mb-px rounded bg-rose-700 px-1 text-xs text-white">
			{ext.toUpperCase()}
		</span>
	{/if}
</div>

<style lang="postcss">
	button.title {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		max-width: 8rem;
	}
	.image:hover .overlay,
	.image.hover .overlay {
		visibility: visible;
		opacity: 1;
	}
	.image.upload {
		display: flex;
		width: 100%;
		flex-shrink: 1;
		align-items: center;
		justify-content: center;
		max-height: 13rem;
		min-height: 6rem;
	}
	.image.multiple.upload {
		height: 11.13rem;
		width: 11.13rem;
		max-width: 11.13rem;
		max-height: 11.13rem;
	}

	img {
		max-width: 100%;
		max-height: 100%;
	}

	.overlay {
		transition:
			visibility 0.1s,
			opacity 0.2s linear;
		background: rgba(0, 0, 0, 0.3);
	}

	.overlay button {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		cursor: pointer;
	}

	.ico {
		display: flex;
		align-items: center;
		justify-content: center;
		background-color: rgba(255, 255, 255, 0.8);
		border-radius: 100%;
		height: 2.5rem;
		width: 2.5rem;
		font-size: 1.6rem;

		:global(svg) {
			height: 1.25rem;
		}
	}

	.icobtn {
		text-align: center;
		font-size: var(--font-size-xs);
		color: var(--color-white);
		text-shadow:
			-1px 0 #000,
			0 1px #000,
			1px 0 #000,
			0 -1px #000;
	}
</style>
