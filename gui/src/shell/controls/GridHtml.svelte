<script lang="ts">
    import Wysiwyg from '$shell/Wysiwyg.svelte';
    import type { GridHtml } from '$types/data';
    import type { GridField } from '$types/fields';

    export let field: GridField;
    export let item: GridHtml;
    export let index: number;

    let showSettings = false;
</script>

<div class="grid-cell-header">
    <slot edit={() => (showSettings = !showSettings)} />
</div>
<div class="grid-cell-body">
    {#if showSettings}
        <div>Keine Einstellungsmöglichkeiten vorhanden</div>
    {:else}
        <Wysiwyg
            required={false}
            name={field.name + '_' + index}
            bind:value={item.value} />
    {/if}
</div>
