<script lang="ts">
    import type { Snippet } from 'svelte';

    import Modal from '$shell/modal/Modal.svelte';
    import Nav from '$shell/Nav.svelte';
    import Toasts from '$shell/Toasts.svelte';

    type Props = {
        data: any;
        children: Snippet;
    };

    let { data, children }: Props = $props();
</script>

<Modal>
    <div class="flex flex-row relative">
        <Nav collections={data.system.collections} />
        <main class="flex-grow">
            {@render children()}
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
