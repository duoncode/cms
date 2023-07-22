<script lang="ts">
    import type { Node as NodeType } from '$types/data';
    import node from '$lib/node';
    import Node from '$shell/Node.svelte';

    export let data: NodeType;

    async function save() {
        node.create(data.doc, data.type, `collection/${data.collection.slug}`);
    }
</script>

<Node bind:data deletable={false} {save} />
