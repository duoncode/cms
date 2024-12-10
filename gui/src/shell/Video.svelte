<!-- @migration-task Error while migrating Svelte code: Cannot split a chunk that has already been edited (37:30 – "on:click={remove}") -->
<script lang="ts">
    import type { FileItem } from '$types/data';
    import { _ } from '$lib/locale';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';

    export let path: string;
    export let file: FileItem;
    export let loading: boolean;
    export let upload: boolean;
    export let remove: () => void;

    let ext = '';

    $: {
        ext = file.file.split('.').pop()?.toLowerCase();
    }
</script>

<div
    class="video {$$props.class !== undefined ? $$props.class : ''}"
    class:empty={!file}
    class:upload>
    {#if loading}
        {_('Loading ...')}
    {:else}
        <video
            controls
            class="w-full">
            <track kind="captions" />
            <source
                src="{path}/{file.file}"
                type="video/{ext}" />
        </video>
        <div class="controls mt-4">
            {#if remove}
                <button
                    class="text-rose-700"
                    on:click={remove}>
                    <span class="ico">
                        <IcoTrash />
                    </span>
                    <span class="icobtn">{_('Löschen')}</span>
                </button>
            {/if}
        </div>
    {/if}
    {#if ext}
        <span
            class="absolute right-1 bottom-1 rounded text-white bg-rose-700 text-xs px-1 mb-px mr-px">
            {ext.toUpperCase()}
        </span>
    {/if}
</div>

<style lang="postcss">
    .video {
        @apply bg-gray-100 border border-gray-300 p-1 text-center relative w-full;

        video {
        }
    }

    .overlay {
        @apply flex flex-row items-center justify-center gap-2;
        @apply invisible opacity-0;
        @apply absolute top-1 bottom-1 left-1 right-1;
        transition: visibility 0.1s, opacity 0.2s linear;
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
        text-shadow: -1px 0 #000, 0 1px #000, 1px 0 #000, 0 -1px #000;
    }
</style>
