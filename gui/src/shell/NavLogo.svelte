<script lang="ts">
	import Logo from '$shell/Logo.svelte';
	import { system } from '$lib/sys';
</script>

<div class="logo">
	{#if $system.logo}
		<img
			style="width: 80%; display: inline-block; margin: 0 auto;"
			src={$system.logo}
			alt="Panel Logo" />
	{:else}
		<Logo />
		<span>Duon</span>
	{/if}
</div>

<style lang="postcss">
	.logo {
		display: flex;
		align-items: center;
		margin-top: 19px;

		:global(svg) {
			height: var(--s-6);
			width: var(--s-6);
			margin-left: -10px;
		}

		span {
			display: inline-block;
			margin-top: -1px;
			margin-left: var(--s-3);
			font-size: var(--s-5);
			font-weight: 300;
			letter-spacing: var(--s-3);
		}
	}
</style>