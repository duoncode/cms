<script lang="ts">
	import type { Toast } from '$lib/toast';

	type Props = {
		toast: Toast;
	};

	let { toast }: Props = $props();
</script>

<div class="text-left text-white">
	{#if toast.title}
		<h3 class="mt-3 mb-1 font-semibold">{toast.title}</h3>
	{/if}
	{#if toast.message}
		<p>{toast.message}</p>
	{/if}
</div>
