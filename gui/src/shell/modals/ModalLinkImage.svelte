<script lang="ts">
    import { createEventDispatcher } from 'svelte';
    import { system } from '$lib/sys';
    import { _ } from '$lib/locale';

    interface Props {
        node: string;
        file: string;
        current: string;
    }

    let { node, file, current }: Props = $props();

    const path = `${$system.prefix}/media/image/node/${node}/${file}`;
    const dispatch = createEventDispatcher();

    const sendFile = () => dispatch('click', { file: path });
</script>

<button
    class="link-image"
    onclick={sendFile}
    class:active={current && current.endsWith(`/${file}`)}>
    <img
        src="{path}?resize=fit&w=200&h=200"
        alt={_('Vorschau')}
        style="max-height: 90vh" />
</button>

<style lang="postcss">
    .link-image {
        @apply flex items-center justify-center border border-gray-400 rounded;

        width: 190px;
        height: 190px;
        padding: 5px;

        &.active {
            @apply border-emerald-600 bg-emerald-100;
        }
    }
</style>
