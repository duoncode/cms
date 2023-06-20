<script lang="ts">
    import { flip } from 'svelte/animate';
    import type { GridItem } from '$types/data';
    import type { GridField } from '$types/fields';
    import GridControls from './GridControls.svelte';
    import GridImage from './GridImage.svelte';
    import GridHtml from './GridHtml.svelte';
    import GridYoutube from './GridYoutube.svelte';

    export let field: GridField;
    export let data: GridItem[];
    export let node: string;

    const controls = {
        image: GridImage,
        html: GridHtml,
        youtube: GridYoutube,
    };
</script>

<div class="grid grid-cols-12 gap-y-2 gap-x-6">
    {#each data as item, index (item)}
        <div
            class="col-span-{item.colspan} row-span-{item.rowspan}"
            animate:flip={{ duration: 300 }}>
            <GridControls bind:data {index} {item} />
            <svelte:component
                this={controls[item.type]}
                bind:item
                {node}
                {index}
                {field} />
        </div>
    {/each}
</div>
