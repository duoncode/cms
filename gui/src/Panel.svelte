<script>
    import Modal from 'svelte-simple-modal';
    import Router from 'svelte-spa-router';

    import getRoutes from './routes';
    import { logout } from './lib/user';

    export let prefix = null;
</script>

<Modal>
    <main>
        <button on:click={logout}>Logout</button>
        <Router routes={getRoutes(prefix).get()} />
    </main>
</Modal>
