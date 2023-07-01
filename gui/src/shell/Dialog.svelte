<script>
    import { _ } from '$lib/locale';
    import Button from '$shell/Button.svelte';

    export let title = null;
    export let body;
    export let type = 'info';
    export let close;
</script>

<div class="modal {type}">
    {#if title}
        <h2>{title}</h2>
    {/if}
    <div class="body">
        {body}
    </div>
    <div class="controls">
        <Button class="secondary" on:click={close}>{_('OK')}</Button>
    </div>
</div>
