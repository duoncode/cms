<script>
    import { _ } from '$lib/locale';
    import IcoDownload from '$shell/icons/IcoDownload.svelte';
    import Link from '$shell/Link.svelte';

    export let base;
    export let asset;
</script>

<style lang="postcss">
    a.file {
        @apply bg-sky-100 border border-sky-300 p-1 text-center rounded-lg;
        @apply relative flex flex-col items-center justify-center text-sky-600;

        span {
            @apply block mt-1 px-2;
        }
    }
</style>

{#if asset}
    <Link class="file" href="{base}/{asset}" target="_blank">
        <IcoDownload />
        <span>{_('Download file')}</span>
    </Link>
{/if}
