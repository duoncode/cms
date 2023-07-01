<script>
    import { _ } from '$lib/locale';
    import IcoDocument from '$shell/icons/IcoDocument.svelte';
    import IcoDownload from '$shell/icons/IcoDownload.svelte';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';

    export let path;
    export let asset;
    export let remove;
</script>

{#if asset}
    <div class="file pl-4">
        <IcoDocument />
        <div class="flex-grow text-left pl-3 truncate">
            <b class="font-medium">{asset.file}</b>
        </div>
        <IcoDownload />
        <a href="{path}/{asset.file}" target="_blank" class="inline-block pl-2">
            {_('Datei herunterladen')}
        </a>

        <button on:click={remove} class="text-rose-800">
            <span class="inline-block h-4 w-4 ml-4 flex items-center">
                <IcoTrash />
            </span>
        </button>
    </div>
{/if}

<style lang="postcss">
    .file {
        @apply flex flex-row items-center w-full;
        @apply bg-gray-100 border border-gray-300 py-2 px-4 text-center rounded-lg;
        @apply relative text-gray-600;
    }
</style>
