<script>
    import { onMount } from 'svelte';
    import Modal from 'svelte-simple-modal';
    import Router from 'svelte-spa-router';

    import { boot } from './lib/boot';
    import Nav from './shell/Nav.svelte';

    import getRoutes from './routes';

    onMount(boot);
</script>

<Modal>
    <Nav />
    <main>
        <Router routes={getRoutes().get()} />
    </main>
</Modal>
