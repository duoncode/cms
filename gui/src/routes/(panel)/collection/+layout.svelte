<script lang="ts">
	import type { Snippet } from 'svelte';

	import { fade } from 'svelte/transition';

	type Props = {
		data: any;
		children: Snippet;
	};

	let { data, children }: Props = $props();
</script>

{#key data.pathname}
	<div
		in:fade={{ duration: 300, delay: 100 }}
		out:fade={{ duration: 300 }}>
		{@render children()}
	</div>
{/key}

<style lang="postcss">
	div {
		height: 100vh;
		width: 100%;
		overflow: hidden;
	}
</style>
