<script lang="ts">
    import { logoutUser } from '$lib/user';
    import { navVisible } from '$lib/ui';
    import NavLogo from './NavLogo.svelte';
    import { collections } from '$lib/collections';
</script>

<style lang="postcss">
    #nav {
        @apply ml-8;
        display: flex;
        width: var(--s-64);
        flex-direction: column;
        height: 100vh;
        background-color: var(--white);
        border-right: var(--border);
        padding: 0 var(--s-6) var(--s-6);
        box-sizing: border-box;
        transition: all 0.25s ease-in-out;

        &.open {
            margin-left: 0;
        }
    }

    h2 {
        @apply font-medium mt-8;
    }
</style>

<div id="nav" class:open={$navVisible}>
    <NavLogo />

    <h2>Inhalte</h2>
    <ul class="ml-4">
        {#each $collections as collection}
            <li class="my-3">
                <a href="/panel/collection/{collection.slug}">
                    {collection.title}
                </a>
            </li>
        {/each}
    </ul>
    <div class="mt-4">
        <button class="font-medium" on:click={logoutUser}>Logout</button>
    </div>
</div>
