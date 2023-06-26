<script>
    import Modal from 'svelte-simple-modal';
    import Nav from '$shell/Nav.svelte';
    import Toasts from '$shell/Toasts.svelte';

    export let data;
</script>

<style lang="postcss">
    main {
        height: 100vh;
        overflow: hidden;
    }
</style>

<Modal>
    <div class="flex flex-row">
        <Nav collections={data.system.collections} />
        <main class="flex-grow">
            <slot />
        </main>
    </div>
    <Toasts />
</Modal>
