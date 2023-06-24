<script>
    import Modal from 'svelte-simple-modal';
    import Nav from '$shell/Nav.svelte';

    export let data;
</script>

<Modal>
    <div class="flex flex-row">
        <Nav collections={data.system.collections} />
        <main class="flex-grow">
            <slot />
        </main>
    </div>
</Modal>
