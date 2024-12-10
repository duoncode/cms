<script lang="ts">
    import Modal from '$shell/modal/Modal.svelte';
    import Nav from '$shell/Nav.svelte';
    import Toasts from '$shell/Toasts.svelte';

    let { data, children } = $props();
</script>

<Modal>
    <div class="flex flex-row relative">
        <Nav collections={data.system.collections} />
        <main class="flex-grow">
            {@render children?.()}
        </main>
    </div>
    <Toasts />
</Modal>

<style lang="postcss">
    main {
        height: 100vh;
        overflow: hidden;
    }
</style>
