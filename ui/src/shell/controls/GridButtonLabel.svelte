<script lang="ts">
	type Props = {
		value: number | null;
	};

	let { value }: Props = $props();
</script>

{#if value !== null}
	<span
		class="grid-button-label absolute flex items-center justify-center rounded-full bg-rose-800 text-xs font-semibold text-white">
		{value}
	</span>
{/if}

<style lang="postcss">
	span {
		font-size: 9px;
		height: 12px;
		width: 12px;
		line-height: 9px;
		top: -13px;
		left: 3px;
	}
</style>
