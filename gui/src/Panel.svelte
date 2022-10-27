<script>
    import { onMount } from 'svelte';
    import Modal from 'svelte-simple-modal';
    import Router from 'svelte-spa-router';

    import sys from './lib/sys';
    import { authenticated } from './lib/user';
    import Nav from './shell/Nav.svelte';

    import getRoutes from './routes';

    onMount(sys.boot);
</script>

{#if $authenticated}
    <Modal>
        <Nav />
        <main>
            <Router routes={getRoutes().get()} />
        </main>
    </Modal>
{/if}
