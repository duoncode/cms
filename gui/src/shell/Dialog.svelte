<script>
    import { _ } from '$lib/locale';

    export let title = null;
    export let body;
    export let type = 'info';
    export let close;
</script>

<div class="message {type}">
    {#if title}
        <h2>{title}</h2>
    {/if}
    <div class="body">
        {body}
    </div>
    <div class="controls">
        <button class="button" on:click={close}>{_('Close')}</button>
    </div>
</div>
