<script lang="ts">
	import type { Snippet } from 'svelte';

	import LocaleTabs from '$shell/LocaleTabs.svelte';

	type Props = {
		translate?: boolean;
		lang?: string | null;
		children: Snippet;
	};

	let { translate = false, lang = $bindable(null), children }: Props = $props();
</script>

<div class="label flex flex-row">
	<div class="flex-grow">{@render children()}</div>
	{#if translate}
		<LocaleTabs bind:lang />
	{/if}
</div>
