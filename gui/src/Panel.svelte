<script>
    import { onMount } from 'svelte';
    import Modal from 'svelte-simple-modal';
    import { link } from 'svelte-spa-router';
    import Router from 'svelte-spa-router';

    import getRoutes from './routes';
    import { logoutUser } from './lib/user';
    import { boot, system } from './lib/boot';

    onMount(boot);
</script>

<Modal>
    <button on:click={logoutUser}>Logout</button>
    <p>
        <a href="/" use:link>Dashboard</a> |
        <a href="/pages" use:link>Pages</a>
    </p>
    {#each $system.sections as section}
        <h2>{section.title}</h2>
    {/each}
    <main>
        <Router routes={getRoutes().get()} />
    </main>
</Modal>
