<script lang="ts">
    import { getContext } from 'svelte';
    import { flip } from 'svelte/animate';
    import { setDirty } from '$lib/state';
    import ModalAdd from '$shell/modals/ModalAdd.svelte';
    import type {
        GridItem,
        GridBase,
        GridHtml as GridHtmlData,
        GridImage as GridImageData,
        GridYoutube as GridYoutubeData,
    } from '$types/data';
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
    const types = [
        { id: 'html', label: 'Text' },
        { id: 'image', label: 'Bild' },
        { id: 'youtube', label: 'Youtube-Video' },
    ];
    const { open, close } = getContext('simple-modal');

    function add(
        index: number,
        before: boolean,
        type: 'html' | 'image' | 'youtube',
    ) {
        let content: GridBase = {
            type,
            colspan: 12,
            rowspan: 1,
        };
        if (type === 'html') {
            (content as GridHtmlData).value = '';
        } else if (type === 'image') {
            (content as GridImageData).files = [];
        } else {
            (content as GridYoutubeData).id = '';
        }

        if (before) {
            data.splice(index, 0, content as GridItem);
        } else {
            if (data.length - 1 === index) {
                data.push(content as GridItem);
            } else {
                data.splice(index + 1, 0, content as GridItem);
            }
        }

        data = data;
        setDirty();
    }

    function openAddModal(index: number) {
        return () => {
            open(
                ModalAdd,
                {
                    index,
                    add,
                    close,
                    types,
                },
                {
                    closeButton: false,
                    styleWindow: { width: '45rem' },
                    styleContent: { overflow: 'hidden', 'overflow-y': 'auto' },
                },
            );
        };
    }
</script>

<div class="grid grid-cols-12 gap-y-2 gap-x-6">
    {#each data as item, index (item)}
        <div
            class="col-span-{item.colspan} row-span-{item.rowspan}"
            animate:flip={{ duration: 300 }}>
            <GridControls
                bind:data
                {index}
                bind:item
                on:addcontent={openAddModal(index)} />
            <svelte:component
                this={controls[item.type]}
                bind:item
                {node}
                {index}
                {field} />
        </div>
    {/each}
</div>
