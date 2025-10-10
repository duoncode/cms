<script lang="ts">
	import { system } from '$lib/sys';

	type Props = {
		lang: string;
	};

	let { lang = $bindable() }: Props = $props();
</script>

<span class="locale-tabs flex-shrink">
	{#each $system.locales as locale (locale)}
		<button
			class="locale-tab text-sm"
			class:active={locale.id === lang}
			onclick={() => (lang = locale.id)}>
			{locale.id.toUpperCase()}
		</button>
	{/each}
</span>

<style lang="postcss">
	.locale-tab {
		display: inline-block;
		box-shadow: 0;
		padding: 0 0.5rem;
		font-weight: normal;

		&.active {
			border-radius: var(--radius);
			background-color: var(--color-gray-200);
			color: var(--color-black);
		}
	}
</style>
