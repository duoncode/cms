<script lang="ts">
	import type { Snippet } from 'svelte';

	type Props = {
		children: Snippet;
		class?: string | null;
	};

	let { children, class: classes = null }: Props = $props();
</script>

<div class="flex-1 {classes}">
	<div class="mx-auto mb-12 max-w-7xl border border-gray-200 bg-white pt-2 pb-4 shadow">
		{@render children()}
	</div>
</div>
