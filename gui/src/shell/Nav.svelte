<script>
    import { fade } from 'svelte/transition';
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
        width: var(--s-64);
        flex-direction: column;
        height: 100vh;
        background-color: var(--white);
        border: var(--border);
        padding: var(--s-5) var(--s-6);
        box-sizing: border-box;
        transition: all 0.25s ease-in-out;

        &.open {
            margin-left: 0;
        }

        &.close {
            margin-left: calc(var(--s-64) * -1);
        }
    }

    #backdrop {
        position: fixed;
        width: 100%;
        height: 100vh;
        background-color: rgb(75 85 99 / 0.75);
    }

    .logo {
        display: flex;
        align-items: center;

        img {
            height: var(--s-6);
            margin-left: -10px;
        }

        span {
            margin-left: var(--s-3);
            font-size: var(--s-5);
            font-weight: 300;
            letter-spacing: var(--s-3);
        }
    }

    @media (--lg) {
        #nav {
            position: relative;
            margin-left: 0;
        }

        #backdrop {
            display: none;
        }
    }
</style>

{#if $navVisible}
    <div id="backdrop" in:fade out:fade />
{/if}
<div id="nav" class:open={$navVisible} class:close={!$navVisible}>
    <NavClose />
    <div class="logo">
        <img src="logo.svg" alt="Logo" />
        <span>CONIA</span>
    </div>
    {#each $system.sections as section}
        <h2>{section.title}</h2>
    {/each}
    <p>
        <a href="/" use:link>Dashboard</a> |
        <a href="/pages" use:link>Pages</a>
    </p>
    <button on:click={logoutUser}>Logout</button>
</div>
