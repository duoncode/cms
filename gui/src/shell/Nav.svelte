<script lang="ts">
    import { logoutUser } from '$lib/user';
    import { navVisible } from '$lib/ui';
    import { collections } from '$lib/collections';
    import NavLogo from '$shell/NavLogo.svelte';
    import Link from '$shell/Link.svelte';
</script>

<div id="nav" class:open={$navVisible}>
    <NavLogo />

    <h2>Inhalte</h2>
    <ul class="ml-4">
        {#each $collections as collection}
            <li class="my-3">
                <Link href="collection/{collection.slug}">
                    {collection.name}
                </Link>
            </li>
        {/each}
    </ul>
    <div class="mt-4">
        <button class="font-medium" on:click={logoutUser}>Logout</button>
    </div>
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
        @apply font-medium mt-8;
    }
</style>
