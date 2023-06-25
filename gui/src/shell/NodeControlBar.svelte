<script lang="ts">
    import type { Document } from '$types/data';
    import { _ } from '$lib/locale';
    import toast from '$lib/toast';
    import req from '$lib/req';
    import NavToggle from '$shell/NavToggle.svelte';
    import Button from '$shell/Button.svelte';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoSave from '$shell/icons/IcoSave.svelte';

    export let doc: Document;
    export let uid: string;

    function remove() {
        console.log('remove' + uid);
    }

    async function save() {
        if (uid === '-new-') {
            req.post(`node/${uid}`, doc);
        } else {
            let response = await req.put(`node/${uid}`, doc);

            if (response.ok) {
                toast.add({
                    kind: 'success',
                    message: _('Dokument erfolgreich gespeichert!'),
                });
            } else {
                toast.add({
                    kind: 'success',
                    message: _(
                        'Fehler beim Speichern des Dokuments aufgetreten!',
                    ),
                });
            }
        }
    }
</script>

<div class="headerbar">
    <NavToggle />
    <div class="controls flex flex-row gap-4 justify-end px-4 py-6">
        <Button class="danger" icon={IcoTrash} on:click={remove}>
            Löschen
        </Button>
        <Button class="primary" icon={IcoSave} on:click={save}>
            Speichern
        </Button>
    </div>
</div>
