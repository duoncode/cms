<script lang="ts">
    import { system } from '$lib/sys';

    type Props = {
        node: string;
        file: string;
        current: string;
        clickFile: (path: string) => void;
    };

    let { node, file, current = $bindable(), clickFile }: Props = $props();

    const path = `${$system.assets}/node/${node}/${file}`;
    const onclick = () => clickFile(path);
</script>

<div class="mt-2 pr-4">
    <button
        {onclick}
        class="px-3 py-2 w-full border border-emerald-600 text-emerald-600 gap-x-1.5 inline-flex items-center rounded-md text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
        class:active={current && current.endsWith(`/${file}`)}>
        {file}
    </button>
</div>

<style lang="postcss">
    button.active {
        @apply bg-emerald-600 text-white;
    }
</style>
