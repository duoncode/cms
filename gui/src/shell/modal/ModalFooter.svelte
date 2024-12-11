<script lang="ts">
	import type { Snippet } from 'svelte';

	let { children, class: classes = '' }: { children: Snippet; class?: string } = $props();
</script>

<div class="modal-footer rounded-b border-t p-4 md:p-8 {classes}">
	{@render children()}
</div>

<style lang="postcss">
	.modal-footer {
		border-color: var(--border-color);
	}
</style>
