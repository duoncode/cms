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

    let sorterElement: HTMLElement;

    function createSorter() {
        if (sorterElement) {
            Sortable.create(sorterElement, {
                animation: 200,
                onUpdate: function (event: SortableEvent) {
                    const tmp = assets[event.oldIndex];

                    assets.splice(event.oldIndex, 1);
                    assets.splice(event.newIndex, 0, tmp);
                },
            });
        }
    }

    function edit(index: number, hasAlt: boolean) {
        const apply = (asset: FileItem) => {
            assets[index] = asset;
            modal.close();
        };

        modal.open(ModalEditImage, {
            asset: assets[index],
            close: modal.close,
            apply,
            translate,
            hasAlt,
        });
    }

    onMount(createSorter);
</script>

{#if multiple && image}
    <div class="multiple-images" bind:this={sorterElement}>
        {#each assets as asset, index (asset)}
            <Image
                upload
                {multiple}
                {path}
                image={asset}
                remove={() => remove(index)}
                edit={() => edit(index, true)}
                {loading} />
        {/each}
    </div>
{:else if !multiple && image && assets && assets.length > 0}
    <Image
        upload
        {path}
        {multiple}
        image={assets[0]}
        remove={() => remove(null)}
        edit={() => edit(0, true)}
        {loading} />
{:else if multiple && file}
    <div
        class="multiple-files flex flex-col gap-3 mb-3"
        bind:this={sorterElement}>
        {#each assets as asset, index (asset)}
            <File
                {path}
                {loading}
                {asset}
                remove={() => remove(index)}
                edit={() => edit(index, false)} />
        {/each}
    </div>
{:else if assets && assets.length > 0}
    <File
        {path}
        {loading}
        asset={assets[0]}
        remove={() => remove(null)}
        edit={() => edit(0, false)} />
{/if}

<style lang="postcss">
    .multiple-images {
        @apply flex flex-row flex-wrap justify-start gap-4 py-4;
    }
</style>
