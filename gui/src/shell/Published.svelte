<script lang="ts">
    import { _ } from '$lib/locale';

    interface Props {
        large?: boolean;
        published?: boolean;
    }

    let { large = false, published = false }: Props = $props();
</script>

<span
    class:large
    class:published
    title={published ? _('Veröffentlich') : _('Unveröffentlicht')}></span>

<style lang="postcss">
    span {
        @apply inline-block rounded-full bg-gray-300;

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
