<script>
    import { link } from 'svelte-spa-router';
    import { system } from '../lib/boot';
    import { logoutUser } from '../lib/user';
    import { navVisible } from '../lib/ui';
    import NavClose from './NavClose.svelte';
</script>

<style type="postcss">
    #nav {
        position: fixed;
        display: flex;
        width: var(--w-64);
        flex-direction: column;
        height: 100vh;
        background-color: var(--white);
        border: var(--border);
        padding: var(--sz-6);
        box-sizing: border-box;
        transition: all 0.25s ease-in-out;

        &.open {
            margin-left: 0;
        }

        &.close {
            margin-left: calc(var(--w-64) * -1);
        }
    }

    @media (--lg) {
        #nav {
            position: relative;
            margin-left: 0;
        }
    }
</style>

<div id="nav" class:open={$navVisible} class:close={!$navVisible}>
    <NavClose />
    {#each $system.sections as section}
        <h2>{section.title}</h2>
    {/each}
    <p>
        <a href="/" use:link>Dashboard</a> |
        <a href="/pages" use:link>Pages</a>
    </p>
    <button on:click={logoutUser}>Logout</button>
</div>
