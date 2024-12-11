<script lang="ts">
	import type { Snippet } from 'svelte';

	type Props = {
		children: Snippet;
	};

	let { children }: Props = $props();
</script>

<div class="flex-1">
	<div class="mx-auto mb-12 max-w-7xl border border-gray-200 bg-white pb-4 pt-2 shadow">
		{@render children()}
	</div>
</div>
