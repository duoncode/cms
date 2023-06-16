<script lang="ts">
    import NodeControlBar from '$shell/NodeControlBar.svelte';
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

    svg {
        color: #999;
        display: inline-block;
    }
</style>

<NodeControlBar uid={data.node.uid} title={data.node.title} />

<div class="max-w-7xl mx-auto mt-8 flex flex-row items-center gap-3">
    <svg
        xmlns="http://www.w3.org/2000/svg"
        height="1em"
        viewBox="0 0 576 512"
        fill="currentColor"
        ><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path
            d="M64 32C64 14.3 49.7 0 32 0S0 14.3 0 32v96V384c0 35.3 28.7 64 64 64H256V384H64V160H256V96H64V32zM288 192c0 17.7 14.3 32 32 32H544c17.7 0 32-14.3 32-32V64c0-17.7-14.3-32-32-32H445.3c-8.5 0-16.6-3.4-22.6-9.4L409.4 9.4c-6-6-14.1-9.4-22.6-9.4H320c-17.7 0-32 14.3-32 32V192zm0 288c0 17.7 14.3 32 32 32H544c17.7 0 32-14.3 32-32V352c0-17.7-14.3-32-32-32H445.3c-8.5 0-16.6-3.4-22.6-9.4l-13.3-13.3c-6-6-14.1-9.4-22.6-9.4H320c-17.7 0-32 14.3-32 32V480z" /></svg>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5">
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <span>
        <a
            href="/panel/collection/{data.collection.slug}"
            class="hover:underline">
            {data.collection.title}
        </a>
    </span>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-5 h-5">
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
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
