<script lang="ts">
	import { system } from '$lib/sys';

	type Props = {
		lang: string;
	};

	let { lang = $bindable() }: Props = $props();
</script>

<span class="locale-tabs">
	{#each $system.locales as locale}
		<button
			class="locale-tab"
			class:active={locale.id === lang}
			onclick={() => (lang = locale.id)}>
			{locale.id.toUpperCase()}
		</button>
	{/each}
</span>

<style lang="postcss">
	@reference "tailwindcss";

	.locale-tabs {
		@apply flex-shrink;
	}

	.locale-tab {
		@apply text-sm;
		display: inline-block;
		box-shadow: 0;
		padding: 0 0.5rem;
		font-weight: normal;

		&.active {
			@apply rounded bg-gray-200 text-black;
		}
	}
</style>
