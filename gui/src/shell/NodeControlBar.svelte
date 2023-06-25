<script lang="ts">
    import { _ } from '$lib/locale';
    import NavToggle from '$shell/NavToggle.svelte';
    import Button from '$shell/Button.svelte';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoSave from '$shell/icons/IcoSave.svelte';

    export let uid: string;

    function remove() {
        console.log('remove' + uid);
    }

    function save() {
        console.log('save' + uid);
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
