<script lang="ts">
    import type { Node } from '$types/data';
    import { _ } from '$lib/locale';
    import { system } from '$lib/sys';
    import { generatePaths } from '$lib/urlpaths';
    import NodeControlBar from '$shell/NodeControlBar.svelte';
    import Breadcrumbs from '$shell/Breadcrumbs.svelte';
    import Headline from '$shell/Headline.svelte';
    import Document from '$shell/Document.svelte';
    import Pane from '$shell/Pane.svelte';
    import Tabs from '$shell/Tabs.svelte';
    import Content from '$shell/Content.svelte';
    import Settings from '$shell/Settings.svelte';

    export let node: Node;
    export let deletable: boolean;
    export let save: () => void;

    let activeTab = 'content';

    function changeTab(tab: string) {
        return () => {
            activeTab = tab;
        };
    }

    $: {
        if (node.doc.route) {
            node.doc.generatedPaths = generatePaths(node.doc, node.doc.route, $system);
        }
    };
</script>

<div class="flex flex-col h-screen">
    <NodeControlBar
        bind:uid={node.uid}
        collectionPath="collection/{node.collection.slug}"
        {deletable}
        {save} />
    <Document>
        <Breadcrumbs slug={node.collection.slug} name={node.collection.name} />
        <Headline>{@html node.title}</Headline>
        <Tabs>
            <button
                on:click={changeTab('content')}
                class:active={activeTab === 'content'}
                class="tab">
                {_('Inhalt')}
            </button>
            {#if node.doc.nodetype === 'page'}
            <button
                on:click={changeTab('settings')}
                class:active={activeTab === 'settings'}
                class="tab">
                {_('Einstellungen')}
            </button>
            {/if}
        </Tabs>
        <Pane>
            {#if activeTab === 'content'}
                <Content bind:fields={node.fields} bind:doc={node.doc} />
            {:else}
                <Settings bind:doc={node.doc} />
            {/if}
        </Pane>
    </Document>
</div>
