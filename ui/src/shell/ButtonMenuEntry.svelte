<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { HTMLButtonAttributes } from 'svelte/elements';

	type Props = {
		children: Snippet;
	};

	let { children, ...attributes }: Props & HTMLButtonAttributes = $props();
</script>

<button
	class="block px-4 py-2 text-sm"
	role="menuitem"
	tabindex="-1"
	{...attributes}>
	{@render children()}
</button>
