<script lang="ts">
    import type { Node } from '$types/data';
    import { _ } from '$lib/locale';
    import NodeControlBar from '$shell/NodeControlBar.svelte';
    import Breadcrumbs from '$shell/Breadcrumbs.svelte';
    import Headline from '$shell/Headline.svelte';
    import Document from '$shell/Document.svelte';
    import Pane from '$shell/Pane.svelte';
    import Tabs from '$shell/Tabs.svelte';
    import Content from '$shell/Content.svelte';
    import Settings from '$shell/Settings.svelte';

    export let data: Node;

    let activeTab = 'content';

    function changeTab(tab: string) {
        return () => {
            activeTab = tab;
        };
    }
</script>

<NodeControlBar
    bind:uid={data.uid}
    bind:doc={data.doc}
    collectionPath="collection/{data.collection.slug}" />
<Document>
    <Breadcrumbs slug={data.collection.slug} name={data.collection.name} />
    <Headline>{data.title}</Headline>
    <Tabs>
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
    </Tabs>
    <Pane>
        {#if activeTab === 'content'}
            <Content bind:fields={data.fields} bind:doc={data.doc} />
        {:else}
            <Settings bind:doc={data.doc} />
        {/if}
    </Pane>
</Document>
