<script lang="ts">
	import type { Snippet } from 'svelte';

	let { children, class: classes = '' }: { children: Snippet; class?: string } = $props();
</script>

<div class="modal-body space-y-4 p-4 md:p-8 {classes}">
	{@render children()}
</div>
