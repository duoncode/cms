<script lang="ts">
    import type { GridItem } from '$types/data';
    import type { GridField } from '$types/fields';
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

<div class="grid grid-cols-12">
    {#each data as item, index}
        <svelte:component
            this={controls[item.type]}
            data={item}
            {node}
            {index}
            {field} />
    {/each}
</div>
