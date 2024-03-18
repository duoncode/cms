<script lang="ts">
    import type { Modal } from 'svelte-simple-modal';
    import type {
        GridItem,
        GridBase,
        GridHtml as GridHtmlData,
        GridImage as GridImageData,
        GridYoutube as GridYoutubeData,
        GridIframe as GridIframeData,
        GridType,
    } from '$types/data';
    import type { GridField } from '$types/fields';

    import { _ } from '$lib/locale';
    import resize from '$lib/resize';
    import { getContext } from 'svelte';
    import { flip } from 'svelte/animate';
    import { setDirty } from '$lib/state';
    import IcoCirclePlus from '$shell/icons/IcoCirclePlus.svelte';
    import Button from '$shell/Button.svelte';
    import ModalAdd from '$shell/modals/ModalAdd.svelte';
    import GridControls from './GridControls.svelte';
    import GridImage from './GridImage.svelte';
    import GridImages from './GridImages.svelte';
    import GridHtml from './GridHtml.svelte';
    import GridYoutube from './GridYoutube.svelte';
    import GridIframe from './GridIframe.svelte';
    import GridVideo from './GridVideo.svelte';

    export let field: GridField;
    export let data: GridItem[];
    export let node: string;
    export let cols = 12;

    const controls = {
        image: GridImage,
        html: GridHtml,
        youtube: GridYoutube,
        images: GridImages,
        video: GridVideo,
        iframe: GridIframe,
    };
    const types = [
        { id: 'html', label: 'Text' },
        { id: 'image', label: 'Einzelbild' },
        { id: 'youtube', label: 'Youtube-Video' },
        { id: 'images', label: 'Mehrere Bilder' },
        { id: 'video', label: 'Video' },
        { id: 'iframe', label: 'Iframe' },
    ];
    const modal: Modal = getContext('simple-modal');

    function add(index: number, before: boolean, type: GridType) {
        let content: GridBase = {
            type,
            colspan: 12,
            rowspan: 1,
            colstart: null,
        };
        if (type === 'html') {
            (content as GridHtmlData).value = '';
        } else if (type === 'image' || type === 'images') {
            (content as GridImageData).files = [];
        } else if (type === 'youtube') {
            (content as GridYoutubeData).value = '';
            (content as GridYoutubeData).aspectRatioX = 16;
            (content as GridYoutubeData).aspectRatioY = 9;
        } else if (type === 'iframe') {
            (content as GridIframeData).value = '';
        }

        if (!data) {
            data = [];
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
            modal.open(
                ModalAdd,
                {
                    index,
                    add,
                    close: modal.close,
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

    function resizeCell(item: GridItem) {
        return element => (item.width = element.clientWidth);
    }
</script>

<div class="grid-field grid grid-cols-{cols} gap-3 rounded p-3 bg-gray-200 border border-gray-300">
    {#if data && data.length > 0}
        {#each data as item, index (item)}
            <div
                class="col-span-{item.colspan} row-span-{item.rowspan} {item.colstart === null
                    ? ''
                    : 'col-start-' +
                      item.colstart} border rounded px-2 pb-2 border-gray-300 bg-white relative"
                animate:flip={{ duration: 300 }}
                use:resize={resizeCell(item)}>
                <svelte:component
                    this={controls[item.type]}
                    bind:item
                    {node}
                    {index}
                    {field}
                    let:edit>
                    <GridControls
                        bind:data
                        bind:item
                        {index}
                        {field}
                        {edit}
                        on:addcontent={openAddModal(index)} />
                </svelte:component>
            </div>
        {/each}
    {:else}
        <div class="p-4 col-span-{cols} flex flex-row justify-center">
            <Button
                class="secondary"
                on:click={openAddModal(null)}>
                <span class="h-5 w-5 mr-2">
                    <IcoCirclePlus />
                </span>
                {_('Inhalt hinzfügen')}
            </Button>
        </div>
    {/if}
</div>
