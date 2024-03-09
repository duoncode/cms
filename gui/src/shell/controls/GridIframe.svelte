<script lang="ts">
    import type { GridIframe } from '$types/data';
    import type { GridField } from '$types/fields';

    export let field: GridField;
    export let item: GridIframe;
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
        <textarea
            class="iframe"
            id={`${field.name}_${index}`}
            name={`${field.name}_${index}`}
            bind:value={item.value} />
    {/if}
</div>
