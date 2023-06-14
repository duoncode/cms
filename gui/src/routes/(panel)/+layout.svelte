<script>
    import { onMount } from 'svelte';
    import Modal from 'svelte-simple-modal';

    import { authenticated } from '$lib/user';
    import Nav from '$shell/Nav.svelte';

    export let data;
</script>

{#if $authenticated && data}
    <Modal>
        <div class="flex flex-row">
            <Nav collections={data.system.collections} />
            <main class="flex-grow">
                <slot />
            </main>
        </div>
    </Modal>
{/if}
