<script lang="ts">
    import type { Node as NodeType } from '$types/data';
    import node from '$lib/node';
    import Node from '$shell/Node.svelte';

    export let data: NodeType;

    async function save(publish: boolean) {
        if (publish) {
            data.doc.published = true;
        }

        node.create(data.doc, data.type, `collection/${data.collection.slug}`);
    }
</script>

<Node
    bind:node={data}
    {save} />
