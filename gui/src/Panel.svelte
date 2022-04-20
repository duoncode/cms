<script>
    import Modal from 'svelte-simple-modal';
    import { link } from 'svelte-spa-router';
    import Router from 'svelte-spa-router';

    import getRoutes from './routes';
    import { logoutUser } from './lib/user';
</script>

<Modal>
    <main>
        <button on:click={logoutUser}>Logout</button>
        <p>
            <a href="/" use:link>Dashboard</a> |
            <a href="/pages" use:link>Pages</a>
        </p>
        <Router routes={getRoutes().get()} />
    </main>
</Modal>
