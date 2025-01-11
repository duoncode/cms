<script lang="ts">
	import { _ } from '$lib/locale';

	type Props = {
		large?: boolean;
		published?: boolean;
	};

	let { large = false, published = false }: Props = $props();
</script>

<span
	class:large
	class:published
	title={published ? _('veröffentlicht') : _('unveröffentlicht')}>
	{#if large}
		{published ? _('veröffentlicht') : _('unveröffentlicht')}
	{/if}
</span>

<style lang="postcss">
	span {
		@apply inline-block rounded-full bg-gray-300 text-sm;

		height: 0.4rem;
		width: 0.4rem;

		&.large {
			@apply px-2;
			height: auto;
			width: auto;
		}

		&.published {
			@apply bg-green-600 text-white;
		}
	}
</style>
