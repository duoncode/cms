<script lang="ts">
	import { _ } from '$lib/locale';
	import { collections } from '$lib/collections';
	import { navVisible } from '$lib/ui';
	import { logoutUser } from '$lib/user';
	import Link from '$shell/Link.svelte';
	import NavLogo from '$shell/NavLogo.svelte';
	import NavTree from '$shell/NavTree.svelte';
</script>

<div
	id="nav"
	class:open={$navVisible}>
	<NavLogo />

	<NavTree items={$collections} />
	<h2 class="cms-nav-section-title">{_('Benutzer')}</h2>
	<div class="cms-nav-link-row"><Link href="userprofile">{_('Mein Benutzerprofil')}</Link></div>
	<div class="cms-nav-link-row"><button onclick={logoutUser}>{_('Abmelden')}</button></div>
</div>

<style lang="postcss">
	#nav {
		width: 16rem;
		margin-left: -16rem;
		display: flex;
		flex-direction: column;
		height: 100vh;
		background-color: var(--cms-color-white);
		border-right: var(--cms-border);
		padding: 0 var(--cms-space-6) var(--cms-space-6);
		box-sizing: border-box;
		transition: all 0.15s ease-in-out;

		&.open {
			margin-left: 0;
		}

		:global(a),
		button {
			font-size: var(--cms-font-size-sm);
			color: var(--cms-color-high);
		}

		button {
			text-align: left;
		}
	}

	.cms-nav-section-title {
		margin-top: var(--cms-space-6);
		font-size: var(--cms-font-size-sm);
		font-weight: 600;
	}

	.cms-nav-link-row {
		margin-top: var(--cms-space-1);
		padding-left: var(--cms-space-4);
	}
</style>
