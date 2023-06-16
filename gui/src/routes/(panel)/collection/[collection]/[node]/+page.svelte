<script lang="ts">
    import NodeControlBar from '$shell/NodeControlBar.svelte';
    import ChevronRight from '$shell/icons/ChevronRight.svelte';
    import DocumentTree from '$shell/icons/DocumentTree.svelte';
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

<NodeControlBar uid={data.node.uid} title={data.node.title} />

<div
    class="breadcrumbs max-w-7xl mx-auto mt-8 flex flex-row items-center gap-3">
    <DocumentTree />
    <ChevronRight />
    <span>
        <a
            href="/panel/collection/{data.collection.slug}"
            class="hover:underline">
            {data.collection.title}
        </a>
    </span>
    <ChevronRight />
    <span class="font-medium">{data.node.title}</span>
</div>

<div
    class="max-w-7xl bg-white border border-gray-200 shadow rounded mx-auto mt-8">
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
                        bind:data={data.node.data.content[field.name]} />
                {:else}
                    {field.type}
                {/if}
            </div>
        {/each}
    </div>
</div>
