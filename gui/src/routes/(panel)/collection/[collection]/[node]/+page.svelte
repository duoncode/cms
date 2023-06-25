<script lang="ts">
    import NodeControlBar from '$shell/NodeControlBar.svelte';
    import IcoChevronRight from '$shell/icons/IcoChevronRight.svelte';
    import IcoDocumentTree from '$shell/icons/IcoDocumentTree.svelte';
    import Link from '$shell/Link.svelte';
    import controls from '$lib/controls';

    export let data;

    function fieldSpan(value: number | null) {
        if (value) {
            if (value > 100 || value <= 0) value = 100;

            return `span ${value} / span ${value}`;
        }

        return 'span 100 / span 100';
    }
</script>

<style lang="postcss">
    .field-grid {
        display: grid;
        grid-template-columns: repeat(100, minmax(0, 1fr));
    }

    .breadcrumbs :global(svg) {
        color: #999;
        display: inline-block;
    }
</style>

<div class="flex flex-col h-full">
    <NodeControlBar bind:uid={data.node.uid} />
    <div
        class="breadcrumbs w-full max-w-7xl mx-auto my-8 flex flex-row items-center gap-3">
        <IcoDocumentTree />
        <IcoChevronRight />
        <span>
            <Link
                href="collection/{data.collection.slug}"
                class="hover:underline">
                {data.collection.name}
            </Link>
        </span>
        <IcoChevronRight />
        <span class="font-medium">{data.title}</span>
    </div>

    <div class="flex-1 overflow-y-auto">
        <div
            class="max-w-7xl bg-white border border-gray-200 mb-12 shadow mx-auto">
            <div class="field-grid">
                {#each data.node.fields as field}
                    <div
                        style="
                    grid-column: {fieldSpan(field.width)};
                    grid-row: {fieldSpan(field.rows)}">
                        {#if controls[field.type]}
                            <svelte:component
                                this={controls[field.type]}
                                {field}
                                node={data.node.data.uid}
                                bind:data={data.node.data.content[
                                    field.name
                                ]} />
                        {:else}
                            {field.type}
                        {/if}
                    </div>
                {/each}
            </div>
        </div>
    </div>
</div>
