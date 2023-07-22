<script lang="ts">
    import { _ } from '$lib/locale';
    import IcoDocument from '$shell/icons/IcoDocument.svelte';
    import IcoDownload from '$shell/icons/IcoDownload.svelte';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoPencil from '$shell/icons/IcoPencil.svelte';

    export let path: string;
    export let asset: string;
    export let remove: () => void;
    export let edit: () => void;
    export let loading: boolean;
</script>

{#if asset}
    <div class="file pl-4">
        <IcoDocument />
        <div class="flex-grow text-left pl-3 truncate">
            <b class="font-medium">{asset}</b>
        </div>
        {#if loading}
            <div>Loading ...</div>
        {/if}
        <IcoDownload />
        <a href="{path}/{asset}" target="_blank" class="inline-block pl-2">
            {_('Datei herunterladen')}
        </a>

        <button on:click={edit} class="text-sky-800">
            <span class="inline-block h-4 w-4 ml-4 flex items-center">
                <IcoPencil />
            </span>
        </button>

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
