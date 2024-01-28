<script lang="ts">
    import type { RoutedNode, Node as NodeType } from '$types/data';
    import req from '$lib/req';
    import { save as saveNode } from '$lib/node';
    import Node from '$shell/Node.svelte';

    export let data: RoutedNode;
    let collection = data.collection;
    let node = data.node;

    async function save(publish: boolean) {
        if (publish) {
            node.published = true;
        }

        const result = await saveNode(node.uid, node);

        if (result.success) {
            const response = await req.get(`node/${result.uid}`);

            if (response.ok) {
                node = response.data as NodeType;
            }
        }
    }
</script>

<Node
    bind:node
    {collection}
    {save} />
