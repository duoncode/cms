<script>
    import { _ } from '$lib/locale';
    import Button from '$shell/Button.svelte';

    export let close;
    export let proceed;
    export let message = null;
</script>

<div class="modal">
    <h2>{_('Dokument löschen')}</h2>
    <div class="body">
        <p>
            {_('Wollen Sie das Dokument wirklich löschen?')}
        </p>
        {#if message}
            <p class="font-semibold mt-6">{message}</p>
        {/if}
    </div>
    <div class="controls">
        <Button class="danger" on:click={proceed}>
            {_('Ja, Dokument löschen')}
        </Button>
        <Button class="primary" on:click={close}>
            {_('Nein, Dokument nicht löschen')}
        </Button>
    </div>
</div>
