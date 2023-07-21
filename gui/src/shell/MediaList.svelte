<script lang="ts">
    import type { Modal } from 'svelte-simple-modal';
    import type { FileItem } from '$types/data';
    import type { SortableEvent } from 'sortablejs';
    import { getContext } from 'svelte';
    import Sortable from 'sortablejs';
    import { onMount } from 'svelte';
    import Image from '$shell/Image.svelte';
    import File from '$shell/File.svelte';
    import ModalEditImage from '$shell/modals/ModalEditImage.svelte';

    export let assets: FileItem[];
    export let multiple: boolean;
    export let translate: boolean;
    export let image: boolean;
    export let loading: boolean;
    export let path: string;
    export let remove: (index: number) => void;

    const file = !image;
    const modal: Modal = getContext('simple-modal');

    let sorter: HTMLElement;

    onMount(() => {
        if (sorter) {
            Sortable.create(sorter, {
                animation: 200,
                onUpdate: function (event: SortableEvent) {
                    const tmp = assets[event.oldIndex];

                    assets.splice(event.oldIndex, 1);
                    assets.splice(event.newIndex, 0, tmp);
                },
            });
        }
    });

    function edit(index: number) {
        const apply = (asset: FileItem) => {
            assets[index] = asset;
            console.log(asset);
            modal.close();
        }

        modal.open(
            ModalEditImage,
            {
                asset: assets[index],
                close: modal.close,
                apply,
                translate,
            }
        );
    }
</script>

{#if assets && assets.length > 0}
    {#if multiple && image}
        <div class="multiple-images" bind:this={sorter}>
            {#each assets as asset, index}
                <Image
                    upload
                    {multiple}
                    {path}
                    image={asset.file}
                    remove={() => remove(index)}
                    edit={() => edit(index)}
                    {loading} />
            {/each}
        </div>
    {:else if !multiple && image}
        <Image
            upload
            {path}
            {multiple}
            image={assets[0] && assets[0].file}
            remove={() => remove(null)}
            edit={() => edit(0)}
            {loading} />
    {:else if multiple && file}
        <div class="multiple-files flex flex-col gap-3 mb-3" bind:this={sorter}>
            {#each assets as asset, index}
                <File
                    {path}
                    {loading}
                    asset={asset.file}
                    remove={() => remove(index)}
                    edit={() => edit(index)} />
            {/each}
        </div>
    {:else}
        <File
            {path}
            {loading}
            asset={assets[0].file}
            remove={() => remove(null)}
            edit={() => edit(0)} />
    {/if}
{/if}

<style lang="postcss">
    .multiple-images {
        @apply flex flex-row flex-wrap justify-start gap-4 py-4;
    }
</style>
