<script lang="ts">
    import type { Node } from '$types/data';
    import { _ } from '$lib/locale';
    import NodeControlBar from '$shell/NodeControlBar.svelte';
    import IcoChevronRight from '$shell/icons/IcoChevronRight.svelte';
    import IcoDocumentTree from '$shell/icons/IcoDocumentTree.svelte';
    import Link from '$shell/Link.svelte';
    import Content from './Content.svelte';
    import Settings from './Settings.svelte';

    export let data: Node;

    let activeTab = 'content';

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

<NodeControlBar
    bind:uid={data.uid}
    bind:doc={data.doc}
    collectionPath="collection/{data.collection.slug}" />
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
                    <Content bind:fields={data.fields} bind:doc={data.doc} />
                {:else}
                    <Settings bind:doc={data.doc} />
                {/if}
            </div>
        </div>
    </div>
</div>
