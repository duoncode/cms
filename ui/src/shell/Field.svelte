<script lang="ts">
	import type { Snippet } from 'svelte';
	import type { SimpleField } from '$types/fields';

	type Props = {
		field: SimpleField;
		class?: string | null;
		children: Snippet;
	};

	let { field, class: classes = null, children }: Props = $props();
</script>

<div
	class="field {classes}"
	class:required={field.required}>
	{@render children()}
	{#if field.description}
		<div class="field-description">
			{field.description}
		</div>
	{/if}
</div>
