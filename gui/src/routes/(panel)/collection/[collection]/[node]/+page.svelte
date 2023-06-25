<script lang="ts">
    import { _ } from '$lib/locale';
    import controls from '$lib/controls';
    import NodeControlBar from '$shell/NodeControlBar.svelte';
    import IcoChevronRight from '$shell/icons/IcoChevronRight.svelte';
    import IcoDocumentTree from '$shell/icons/IcoDocumentTree.svelte';
    import Link from '$shell/Link.svelte';

    export let data;
    let activeTab = 'content';

    function fieldSpan(value: number | null) {
        if (value) {
            if (value > 100 || value <= 0) value = 100;

            return `span ${value} / span ${value}`;
        }

        return 'span 100 / span 100';
    }

    function changeTab(tab: string) {
        return () => {
            activeTab = tab;
        };
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

    .wrapper {
        height: 101%;
    }
</style>

<NodeControlBar bind:uid={data.uid} />
<div class="wrapper overflow-y-scroll">
    <div class="w-full max-w-7xl mx-auto px-8">
        <div class="breadcrumbs mt-8 mb-4 flex flex-row items-center gap-3">
            <IcoDocumentTree />
            <IcoChevronRight />
            <span>
                <Link
                    href="collection/{data.collection.slug}"
                    class="hover:underline">
                    {data.collection.name}
                </Link>
            </span>
        </div>

        <h1 class="text-3xl font-medium mb-6">{data.title}</h1>

        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                        on:click={changeTab('content')}
                        class:active={activeTab === 'content'}
                        class="tab">
                        {_('Inhalt')}
                    </button>
                    <button
                        on:click={changeTab('settings')}
                        class:active={activeTab === 'settings'}
                        class="tab">
                        {_('Einstellungen')}
                    </button>
                </nav>
            </div>
        </div>
        <div class="flex-1">
            <div
                class="max-w-7xl bg-white border border-gray-200 mb-12 shadow mx-auto">
                {#if activeTab === 'content'}
                    <div class="field-grid">
                        {#each data.fields as field}
                            <div
                                style="
                                    grid-column: {fieldSpan(field.width)};
                                    grid-row: {fieldSpan(field.rows)}">
                                {#if controls[field.type]}
                                    <svelte:component
                                        this={controls[field.type]}
                                        {field}
                                        node={data.doc.uid}
                                        bind:data={data.doc.content[
                                            field.name
                                        ]} />
                                {:else}
                                    {field.type}
                                {/if}
                            </div>
                        {/each}
                    </div>
                {:else}
                    SETTINGS
                {/if}
            </div>
        </div>
    </div>
</div>
