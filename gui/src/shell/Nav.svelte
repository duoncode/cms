<script lang="ts">
	import { _ } from '$lib/locale';
	import { logoutUser } from '$lib/user';
	import { navVisible } from '$lib/ui';
	import { collections } from '$lib/collections';
	import NavLogo from '$shell/NavLogo.svelte';
	import Link from '$shell/Link.svelte';
</script>

<div
	id="nav"
	class:open={$navVisible}>
	<NavLogo />

	{#each $collections as item (item)}
		{#if item.type === 'section'}
			<h2>{item.name}</h2>
		{:else}
			<div class="ml-4 mt-1">
				<Link href="collection/{item.slug}">
					{item.name}
				</Link>
			</div>
		{/if}
	{/each}
	<h2>{_('Benutzer')}</h2>
	<div class="ml-4 mt-1"><Link href="userprofile">{_('Mein Benutzerprofil')}</Link></div>
	<div class="ml-4 mt-1"><button onclick={logoutUser}>{_('Abmelden')}</button></div>
</div>

<style lang="postcss">
	#nav {
		width: 16rem;
		margin-left: -16rem;
		display: flex;
		flex-direction: column;
		height: 100vh;
		background-color: var(--white);
		border-right: var(--border);
		padding: 0 var(--s-6) var(--s-6);
		box-sizing: border-box;
		transition: all 0.15s ease-in-out;

		&.open {
			margin-left: 0;
		}
	}

	h2 {
		@apply mt-6 font-semibold;
	}
</style>
