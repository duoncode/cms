<script lang="ts">
    import { _ } from '$lib/locale';
    import Button from '$shell/Button.svelte';

    interface Props {
        close: any;
        proceed: any;
        message?: any;
    }

    let { close, proceed, message = null }: Props = $props();
</script>

<div class="modal">
    <h2>{_('Dokument löschen')}</h2>
    <div class="body">
        <p>
            {_('Soll der Eintrag wirklich gelöscht werden?')}
        </p>
        {#if message}
            <p class="font-semibold mt-6">{message}</p>
        {/if}
    </div>
    <div class="controls">
        <Button
            class="danger"
            on:click={proceed}>
            {_('Ja, Eintrag löschen')}
        </Button>
        <Button
            class="secondary"
            on:click={close}>
            {_('Nein, Eintrag nicht löschen')}
        </Button>
    </div>
</div>
