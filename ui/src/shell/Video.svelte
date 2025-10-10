<script lang="ts">
	import type { FileItem } from '$types/data';
	import { _ } from '$lib/locale';
	import IcoTrash from '$shell/icons/IcoTrash.svelte';

	type Props = {
		path: string;
		file: FileItem;
		loading: boolean;
		upload: boolean;
		remove: () => void;
		class?: string;
	};

	let { path, file, loading, upload, remove, class: classes = '' }: Props = $props();

	let ext = $derived(file.file.split('.').pop()?.toLowerCase());
</script>

<div
	class="video relative w-full border border-gray-300 bg-gray-100 p-1 text-center {classes}"
	class:empty={!file}
	class:upload>
	{#if loading}
		{_('Loading ...')}
	{:else}
		<video
			controls
			class="w-full">
			<track kind="captions" />
			<source
				src="{path}/{file.file}"
				type="video/{ext}" />
		</video>
		<div class="controls mt-4">
			{#if remove}
				<button
					class="text-rose-700"
					onclick={remove}>
					<span class="ico flex items-center justify-center">
						<IcoTrash />
					</span>
					<span class="icobtn text-center text-xs text-white">{_('Löschen')}</span>
				</button>
			{/if}
		</div>
	{/if}
	{#if ext}
		<span
			class="absolute right-1 bottom-1 mr-px mb-px rounded bg-rose-700 px-1 text-xs text-white">
			{ext.toUpperCase()}
		</span>
	{/if}
</div>

<style lang="postcss">
	.ico {
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
		text-shadow:
			-1px 0 #000,
			0 1px #000,
			1px 0 #000,
			0 -1px #000;
	}
</style>
