<script lang="ts">
    import type { Section } from '$lib/sys';
    import { logoutUser } from '$lib/user';
    import { navVisible } from '$lib/ui';
    import Backdrop from './Backdrop.svelte';
    import NavClose from './NavClose.svelte';
    import NavToggle from './NavToggle.svelte';
    import NavLogo from './NavLogo.svelte';

    export let sections: Section[];
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
</style>

<div id="nav" class:open={$navVisible}>
    <NavLogo />
    {#each sections as section}
        <h2>{section.name}</h2>
    {/each}
    <p>
        <a href="/panel/">Dashboard</a> |
        <a href="/panel/pages">Pages</a>
    </p>
    <button on:click={logoutUser}>Logout</button>
</div>
