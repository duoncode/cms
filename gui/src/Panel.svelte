<script>
    import { onMount } from 'svelte';
    import Modal from 'svelte-simple-modal';
    import Router from 'svelte-spa-router';

    import { setup, system } from '$lib/sys';
    import { authenticated } from '$lib/user';
    import Nav from './shell/Nav.svelte';

    import getRoutes from './routes';

    onMount(async () => {
        await setup();
    });
</script>

{#if $authenticated && $system}
    <Modal>
        <Nav sections={$system.sections} />
        <main>
            <Router routes={getRoutes().get()} />
        </main>
    </Modal>
{/if}
