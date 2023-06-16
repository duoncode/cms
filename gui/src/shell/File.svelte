<script>
    import { _ } from '$lib/locale';
    import IcoDownload from '$shell/icons/IcoDownload.svelte';

    export let base;
    export let asset;
</script>

<style lang="postcss">
    a.file {
        @apply bg-blue-100 border border-blue-300 p-1 text-center rounded-lg;
        @apply relative flex flex-col items-center justify-center text-blue-600;

        span {
            @apply block mt-1 px-2;
        }
    }
</style>

{#if asset}
    <a class="file" href="{base}/{asset}" target="_blank">
        <IcoDownload />
        <span>{_('Download file')}</span>
    </a>
{/if}
