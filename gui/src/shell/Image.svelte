<script>
    import { getContext } from 'svelte';
    import { _ } from '$lib/locale';
    import IcoCamera from '$shell/icons/IcoCamera.svelte';
    import IcoCircleSlash from '$shell/icons/IcoCircleSlash.svelte';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoEye from '$shell/icons/IcoEye.svelte';
    import ImagePreview from '$shell/ImagePreview.svelte';

    export let base;
    export let image;
    export let loading;
    export let upload;
    export let remove;
    export let size = 'xl';
    export let altempty = null; // alternative empty image placeholder
    export let useThumb = true;
    export let querystring = '';

    let orig;
    let thumb;
    let hover = false;

    const { open } = getContext('simple-modal');

    function preview() {
        open(ImagePreview, { image: orig });
    }

    $: {
        if (base && image) {
            let ext = image.split('.').pop()?.toLowerCase();

            if (base === '/') {
                orig = `/${image}`;
            } else {
                orig = `${base}/${image}`;
            }

            if (!useThumb || ext === 'svg') {
                thumb = orig;
            } else {
                if (base === '/') {
                    thumb = `/thumbs/${image}`;
                } else {
                    thumb = `${base}/thumbs/${image}`;
                }
            }
        } else {
            if (image) {
                orig = image;
                thumb = image;
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
        @apply flex flex-shrink mx-auto w-full justify-center items-center;
        flex: 0 0 33%;
        max-width: 300px;
    }

    .overlay {
        @apply flex flex-row items-center justify-evenly;
        @apply invisible opacity-0;
        @apply absolute top-1 bottom-1 left-1 right-1;
        transition: visibility 0.1s, opacity 0.2s linear;
        background: rgba(0, 0, 0, 0.3);
    }

    div[class^='icon-'] {
        cursor: pointer;
        flex: 0 0 3rem;
    }

    .ico {
        display: block;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 100%;
        height: 3rem;
        font-size: 1.6rem;
        padding-top: 0.3rem;
    }

    .icobtn {
        @apply text-xs text-white;
        text-shadow: -1px 0 #000, 0 1px #000, 1px 0 #000, 0 -1px #000;
    }

    p {
        @apply text-gray-400;
    }

    /* Stacked Icon */
    :global(.stacked-icons svg) {
        display: block;
    }
    .ban .outer {
        @apply text-red-300;
    }
    .ban .inner {
        @apply text-gray-600;
    }
    .size-lg {
        font-size: 200%;
        width: 4rem;
        height: 4rem;
    }
    .size-xl {
        font-size: 300%;
        width: 6rem;
        height: 6rem;
    }
</style>

<div
    class="image {$$props.class !== undefined ? $$props.class : ''}"
    class:empty={!image}
    class:upload
    class:hover>
    {#if loading}
        {_('Loading ...')}
    {:else if orig}
        <img src="{thumb}{querystring}" alt={_('-thumbnail-')} />
        <div class="overlay">
            {#if remove}
                <button class="text-orange-600" on:click={remove}>
                    <span class="ico">
                        <IcoTrash />
                    </span>
                    <span class="icobtn">{_('Delete')}</span>
                </button>
            {/if}
            <button class="icon-sky-600" on:click={preview}>
                <span class="ico">
                    <IcoEye /><br />
                </span>
                <span class="icobtn">{_('View')}</span>
            </button>
        </div>
    {:else if altempty}
        <img src="{altempty}{querystring}" alt={_('Default image')} />
    {:else}
        <div class="empty">
            <div
                class="stacked-icons inline-block relative"
                class:size-lg={size == 'lg'}
                class:size-xl={size == 'xl'}>
                <div
                    class="inner absolute inset-0 flex justify-center items-center">
                    <IcoCamera />
                </div>
                <div
                    class="outer absolute inset-0 flex justify-center items-center">
                    <IcoCircleSlash />
                </div>
            </div>
            <p>{_('No image')}</p>
        </div>
    {/if}
</div>
