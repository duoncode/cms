<script lang="ts">
    import type { Modal } from 'svelte-simple-modal';
    import { getContext } from 'svelte';
    import { _ } from '$lib/locale';
    import NavToggle from '$shell/NavToggle.svelte';
    import Button from '$shell/Button.svelte';
    import ButtonMenu from '$shell/ButtonMenu.svelte';
    import ButtonMenuEntry from '$shell/ButtonMenuEntry.svelte';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoSave from '$shell/icons/IcoSave.svelte';
    import IcoEye from '$shell/icons/IcoEye.svelte';
    import ModalRemove from '$shell/modals/ModalRemove.svelte';
    import node from '$lib/node';

    export let uid: string;
    export let collectionPath: string;
    export let deletable: boolean;
    export let locked = false;
    export let save: (publish: boolean) => void;
    export let preview: () => void | null;

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
        {#if deletable && !locked}
            <Button class="danger" icon={IcoTrash} on:click={remove}>
                {_('Löschen')}
            </Button>
        {/if}
        {#if preview}
            <Button class="secondary" icon={IcoEye} on:click={preview}>
                {_('Vorschau')}
            </Button>
        {/if}
        {#if !locked}
            <ButtonMenu
                class="primary"
                icon={IcoSave}
                on:click={() => save(false)}
                label={_('Speichern')}
                let:closeMenu>
                <ButtonMenuEntry
                    on:click={() => {
                        save(true), closeMenu();
                    }}>
                    {_('Speichern und veröffentlichen')}
                </ButtonMenuEntry>
            </ButtonMenu>
        {/if}
    </div>
</div>
