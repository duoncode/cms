<script lang="ts">
    import { getContext } from 'svelte';
    import { _ } from '$lib/locale';
    import IcoCamera from '$shell/icons/IcoCamera.svelte';
    import IcoCircleSlash from '$shell/icons/IcoCircleSlash.svelte';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoEye from '$shell/icons/IcoEye.svelte';
    import ImagePreview from '$shell/ImagePreview.svelte';

    export let base: string;
    export let cache: string;
    export let image: string;
    export let loading: boolean;
    export let upload: boolean;
    export let multiple: boolean;
    export let remove: () => void;
    export let size = 'xl';
    export let useThumb = true;
    export let querystring = '';

    let orig: string;
    let thumb: string;
    let hover = false;
    let ext = '';

    const { open } = getContext('simple-modal');

    function preview() {
        open(ImagePreview, { image: orig });
    }

    function thumbIt(image: string) {
        const a = image.split('.');

        return (
            a.slice(0, a.length - 1).join('.') +
            '-w400.' +
            a.slice(a.length - 1)
        );
    }

    $: {
        if (base && cache && image) {
            ext = image.split('.').pop()?.toLowerCase();

            orig = `${base}/${image}`;

            if (!useThumb || ext === 'svg') {
                thumb = orig;
            } else {
                thumb = `${cache}/${thumbIt(image)}`;
            }
        } else {
            if (image) {
                orig = image;
                thumb = thumbIt(image);
            } else {
                orig = null;
                thumb = null;
            }
        }
    }
</script>

<style lang="postcss">
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
    }
    .image.multiple.upload {
        height: 10rem;
        width: 10rem;
        max-width: 10rem;
        max-height: 10rem;
    }

    img {
        max-width: 100%;
        max-height: 100%;
    }

    .overlay {
        @apply flex flex-row items-center justify-center gap-6;
        @apply invisible opacity-0;
        @apply absolute top-1 bottom-1 left-1 right-1;
        transition: visibility 0.1s, opacity 0.2s linear;
        background: rgba(0, 0, 0, 0.3);
    }

    button[class^='icon-'] {
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
        text-shadow: -1px 0 #000, 0 1px #000, 1px 0 #000, 0 -1px #000;
    }

    p {
        @apply text-gray-400;
    }

    /* Stacked Icon */
    :global(.stacked-icons) {
        width: 6rem;
        height: 6rem;
        svg {
            display: block;
        }
        &.ban .outer {
            @apply text-red-300;

            :global(svg) {
                width: 100%;
            }
        }
        &.ban .inner {
            @apply text-gray-600;

            :global(svg) {
                width: 60%;
            }
        }
    }
</style>

<div
    class="image {$$props.class !== undefined ? $$props.class : ''}"
    class:empty={!image}
    class:upload
    class:multiple
    class:hover>
    {#if loading}
        {_('Loading ...')}
    {:else}
        <img src="{thumb}{querystring}" alt={_('Vorschau')} />
        <div class="overlay">
            {#if remove}
                <button class="text-orange-600" on:click={remove}>
                    <span class="ico">
                        <IcoTrash />
                    </span>
                    <span class="icobtn">{_('löschen')}</span>
                </button>
            {/if}
            <button class="icon-sky-600" on:click={preview}>
                <span class="ico">
                    <IcoEye /><br />
                </span>
                <span class="icobtn">{_('anzeigen')}</span>
            </button>
        </div>
    {/if}
    {#if ext}
        <span
            class="absolute right-2 bottom-2 rounded text-white bg-orange-700 text-sm px-1"
            >{ext.toUpperCase()}</span>
    {/if}
</div>
