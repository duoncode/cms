<script lang="ts">
    import { getContext } from 'svelte';
    import type { Modal } from 'svelte-simple-modal';
    import NavToggle from '$shell/NavToggle.svelte';
    import Button from '$shell/Button.svelte';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoSave from '$shell/icons/IcoSave.svelte';
    import ModalRemove from '$shell/modals/ModalRemove.svelte';
    import node from '$lib/node';

    export let uid: string;
    export let collectionPath: string;
    export let allowDelete: boolean;
    export let save: () => void;

    const modal: Modal = getContext('simple-modal');

    async function remove() {
        modal.open(
            ModalRemove,
            {
                close: modal.close,
                proceed: () => {
                    node.remove(uid, collectionPath);
                    modal.close();
                },
            },
            {
                closeButton: false,
            },
        );
    }
</script>

<div class="headerbar">
    <NavToggle />
    <div class="controls flex flex-row gap-4 justify-end px-4 py-6">
        {#if allowDelete}
            <Button class="danger" icon={IcoTrash} on:click={remove}>
                Löschen
            </Button>
        {/if}
        <Button class="primary" icon={IcoSave} on:click={save}>
            Speichern
        </Button>
    </div>
</div>
