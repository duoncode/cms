<script lang="ts">
	type Props = {
		value: number | null;
	};

	let { value }: Props = $props();
</script>

{#if value !== null}
	<span class="grid-button-label absolute block rounded-full bg-rose-800 text-xs text-white">
		{value}
	</span>
{/if}

<style lang="postcss">
	@reference "tailwindcss";

	span {
		@apply flex items-center justify-center font-semibold;
		font-size: 9px;
		height: 12px;
		width: 12px;
		line-height: 9px;
		top: -13px;
		left: 3px;
	}
</style>
