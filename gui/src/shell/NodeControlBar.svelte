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
    import { remove as removeNode } from '$lib/node';

    interface Props {
        uid: string;
        collectionPath: string;
        deletable: boolean;
        locked?: boolean;
        save: (publish: boolean) => void;
        preview: () => void | null;
    }

    let {
        uid,
        collectionPath,
        deletable,
        locked = false,
        save,
        preview
    }: Props = $props();

    const modal: Modal = getContext('simple-modal');

    async function remove() {
        modal.open(
            ModalRemove,
            {
                close: modal.close,
                proceed: () => {
                    removeNode(uid, collectionPath);
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
            <Button
                class="danger"
                icon={IcoTrash}
                on:click={remove}>
                {_('Löschen')}
            </Button>
        {/if}
        {#if preview}
            <Button
                class="secondary"
                icon={IcoEye}
                on:click={preview}>
                {_('Vorschau')}
            </Button>
        {/if}
        {#if !locked}
            <ButtonMenu
                class="primary"
                icon={IcoSave}
                on:click={() => save(false)}
                label={_('Speichern')}
                >
                {#snippet children({ closeMenu })}
                                <ButtonMenuEntry
                        on:click={() => {
                            save(true), closeMenu();
                        }}>
                        {_('Speichern und veröffentlichen')}
                    </ButtonMenuEntry>
                                            {/snippet}
                        </ButtonMenu>
        {/if}
    </div>
</div>
