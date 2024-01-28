<script lang="ts">
    import type { RoutedNode } from '$types/data';
    import { create } from '$lib/node';
    import Node from '$shell/Node.svelte';

    export let data: RoutedNode;
    let collection = data.collection;
    let node = data.node;

    async function save(publish: boolean) {
        if (publish) {
            node.published = true;
        }

        create(node, node.type.handle, `collection/${collection.slug}`);
    }
</script>

<Node
    bind:node
    {collection}
    {save} />
