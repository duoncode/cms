<script lang="ts">
    import type { Node as NodeType } from '$types/data';
    import node from '$lib/node';
    import Node from '$shell/Node.svelte';

    export let data: NodeType;

    async function save() {
        node.save(data.doc.uid, data.doc);
    }
</script>

<Node bind:node={data} {save}/>
