<script lang="ts">
	import type { Toast } from '$lib/toast';

	type Props = {
		toast: Toast;
	};

	let { toast }: Props = $props();
</script>

<div class="text-left text-white">
	{#if toast.title}
		<h3>{toast.title}</h3>
	{/if}
	{#if toast.message}
		<p>{toast.message}</p>
	{/if}
</div>

<style lang="postcss">
	h3,
	h4 {
		@apply mb-1 mt-3 font-semibold;
	}

	h3:first-child {
		@apply mt-0;
	}

	h4 {
		@apply text-gray-200;
	}

	ul {
		@apply ml-4 mt-2 list-disc;
	}
</style>
