<script lang="ts">
    import type { GridImage } from '$types/data';
    import type { GridField } from '$types/fields';
    import Upload from '$shell/Upload.svelte';
    import { system } from '$lib/sys';

    export let field: GridField;
    export let item: GridImage;
    export let node: string;
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
        <Upload
            type="image"
            multiple={false}
            path="{$system.prefix}/media/image/node/{node}"
            name={field.name + '_' + index}
            translate={false}
            bind:assets={item.files} />
    {/if}
</div>
