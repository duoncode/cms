<script>
    import { onMount } from 'svelte';
    import Modal from 'svelte-simple-modal';
    import Router from 'svelte-spa-router';

    import { boot } from './lib/boot';
    import { authenticated } from './lib/user';
    import Nav from './shell/Nav.svelte';

    import getRoutes from './routes';

    onMount(boot);
</script>

{#if $authenticated}
    <Modal>
        <Nav />
        <main>
            <Router routes={getRoutes().get()} />
        </main>
    </Modal>
{/if}
