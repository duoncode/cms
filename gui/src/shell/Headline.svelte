<script lang="ts">
	import type { Snippet } from 'svelte';

	import { dirty } from '$lib/state';
	import { _ } from '$lib/locale';
	import Published from '$shell/Published.svelte';

	type Props = {
		showPublished?: boolean;
		published?: boolean;
		children: Snippet;
	};

	let { showPublished = false, published = false, children }: Props = $props();
</script>

<h1 class="mb-6 flex flex-row items-center justify-start text-3xl font-semibold">
	<span class="flex items-center">
		{@render children()}
	</span>
	<div class="status-bar flex flex-grow flex-row items-center justify-end">
		{#if $dirty}
			<span
				class="dirty-indicator ml-4 rounded-full bg-rose-600 px-2 pb-px text-sm font-bold text-white">
				!
			</span>
		{/if}
		{#if showPublished}
			<span class="ml-3 inline-flex items-center">
				<Published
					{published}
					large />
			</span>
		{/if}
	</div>
</h1>

<style lang="postcss">
	h1 {
		line-height: 36px;
	}
</style>
