<script lang="ts">
    import type { FileItem } from '$types/data';
    import type { ModalFunctions } from '$shell/modal';

    import { getContext } from 'svelte';
    import { system } from '$lib/sys';
    import { _ } from '$lib/locale';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoEye from '$shell/icons/IcoEye.svelte';
    import IcoPencil from '$shell/icons/IcoPencil.svelte';
    import ImagePreview from '$shell/ImagePreview.svelte';

    type Props = {
        path: string;
        image: FileItem;
        loading: boolean;
        upload: boolean;
        multiple: boolean;
        remove: () => void;
        edit: () => void;
        class?: string;
    };

    let {
        path,
        image,
        loading,
        upload,
        multiple,
        remove,
        edit,
        class: classes = '',
    }: Props = $props();

    let { open, close } = getContext<ModalFunctions>('modal');

    let hover = $state(false);
    let ext = $derived(image.file.split('.').pop()?.toLowerCase());
    let orig = $derived(`${path}/${image.file}`);
    let thumb = $derived(ext === 'svg' ? orig : `${path}/${thumbIt(image.file)}`);
    let title = $derived(getTitle(image, 'title') || getTitle(image, 'alt'));

    function preview() {
        open(
            ImagePreview,
            {
                close,
                image: orig,
            },
            {},
        );
    }

    function thumbIt(image: string) {
        return image + '?resize=width&w=400';
    }

    function getTitle(image: FileItem, key: string) {
        if (image[key]) {
            if (typeof image[key] === 'string') {
                return image[key];
            }

            for (const locale of $system.locales) {
                if (image[key][locale.id]) {
                    return image[key][locale.id];
                }
            }
        }

        return '';
    }
</script>

<div
    class="image {classes}"
    class:empty={!image}
    class:upload
    class:multiple
    class:hover>
    {#if loading}
        {_('Loading ...')}
    {:else}
        <img
            src={thumb}
            alt={_('Vorschau')} />
        <div class="overlay">
            {#if remove}
                <button
                    class="text-rose-700"
                    onclick={remove}>
                    <span class="ico">
                        <IcoTrash />
                    </span>
                    <span class="icobtn">{_('Löschen')}</span>
                </button>
            {/if}
            <button
                class="text-sky-700"
                onclick={preview}>
                <span class="ico">
                    <IcoEye />
                </span>
                <span class="icobtn">{_('Vorschau')}</span>
            </button>
            <button
                class="text-sky-700"
                onclick={edit}>
                <span class="ico">
                    <IcoPencil />
                </span>
                <span class="icobtn">{_('Titel')}</span>
            </button>
        </div>
    {/if}
    {#if title}
        <button
            class="title absolute left-1 bottom-1 rounded text-gray-600 bg-white text-xs px-1 mb-px ml-px"
            onclick={edit}>
            {title}
        </button>
    {/if}
    {#if ext}
        <span
            class="absolute right-1 bottom-1 rounded text-white bg-rose-700 text-xs px-1 mb-px mr-px">
            {ext.toUpperCase()}
        </span>
    {/if}
</div>

<style lang="postcss">
    button.title {
        @apply truncate;
        max-width: 8rem;
    }
    .image {
        @apply bg-gray-100 border border-gray-300 p-1 text-center relative;
    }
    .image:hover .overlay,
    .image.hover .overlay {
        visibility: visible;
        opacity: 1;
    }
    .image.upload {
        @apply flex flex-shrink w-full justify-center items-center;
        max-height: 13rem;
        min-height: 6rem;
    }
    .image.multiple.upload {
        height: 11.13rem;
        width: 11.13rem;
        max-width: 11.13rem;
        max-height: 11.13rem;
    }

    img {
        max-width: 100%;
        max-height: 100%;
    }

    .overlay {
        @apply flex flex-row items-center justify-center gap-2;
        @apply invisible opacity-0;
        @apply absolute top-1 bottom-1 left-1 right-1;
        transition:
            visibility 0.1s,
            opacity 0.2s linear;
        background: rgba(0, 0, 0, 0.3);
    }

    .overlay button {
        @apply flex flex-col items-center justify-center;
        cursor: pointer;
    }

    .ico {
        @apply flex justify-center items-center;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 100%;
        height: 2.5rem;
        width: 2.5rem;
        font-size: 1.6rem;

        :global(svg) {
            height: 1.25rem;
        }
    }

    .icobtn {
        @apply text-xs text-white text-center;
        text-shadow:
            -1px 0 #000,
            0 1px #000,
            1px 0 #000,
            0 -1px #000;
    }
</style>
