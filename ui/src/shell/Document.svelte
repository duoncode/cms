<script lang="ts">
	import type { Snippet } from 'svelte';

	type Props = {
		children: Snippet;
	};

	let { children }: Props = $props();
</script>

<div class="flex-1 flex-grow overflow-y-scroll">
	<div class="mx-auto w-full max-w-7xl px-8">
		{@render children()}
	</div>
</div>
