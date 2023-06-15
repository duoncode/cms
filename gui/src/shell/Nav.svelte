<script lang="ts">
    import type { Collection } from '$lib/sys';
    import { logoutUser } from '$lib/user';
    import { navVisible } from '$lib/ui';
    import Backdrop from './Backdrop.svelte';
    import NavClose from './NavClose.svelte';
    import NavToggle from './NavToggle.svelte';
    import NavLogo from './NavLogo.svelte';

    export let collections: Collection[];
</script>

<style lang="postcss">
    #nav {
        display: flex;
        width: var(--s-64);
        flex-direction: column;
        height: 100vh;
        background-color: var(--white);
        border-right: var(--border);
        padding: 0 var(--s-6) var(--s-6);
        box-sizing: border-box;
        transition: all 0.25s ease-in-out;
        margin-left: calc(var(--s-64) * -1);

        &.open {
            margin-left: 0;
        }
    }

    @media (--lg) {
        #nav {
            position: relative;
            margin-left: 0;
        }
    }

    h2 {
        @apply font-bold mt-4;
    }
</style>

<div id="nav" class:open={$navVisible}>
    <NavLogo />

    <p class="mt-8"><a href="/panel">Dashboard</a></p>
    <h2>Inhalte</h2>
    <ul>
        {#each collections as collection}
            <li>
                <a href="/panel/collection/{collection.slug}">
                    {collection.title}
                </a>
            </li>
        {/each}
    </ul>
    <div class="mt-4">
        <button on:click={logoutUser}>Logout</button>
    </div>
</div>
