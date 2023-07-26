<script lang="ts">
    import { _ } from '$lib/locale';

    export let large = false;
    export let published = false;
</script>

<span
    class:large
    class:published
    title={published ? _('Veröffentlich') : _('Unveröffentlicht')} />

<style lang="postcss">
    span {
        @apply inline-block rounded-full bg-gray-400;

        width: 0.4rem;
        height: 0.4rem;

        &.large {
            width: 0.5rem;
            height: 0.5rem;
        }

        &.published {
            @apply bg-green-600;
        }
    }
</style>
