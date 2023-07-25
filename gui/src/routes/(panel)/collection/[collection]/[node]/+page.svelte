<script lang="ts">
    import type { Document, Node as NodeType } from '$types/data';
    import req from '$lib/req';
    import node from '$lib/node';
    import Node from '$shell/Node.svelte';

    export let data: NodeType;

    async function save(publish: boolean) {
        if (publish) {
            data.doc.published = true;
        }

        const result = await node.save(data.doc.uid, data.doc);

        if (result.success) {
            const response = await req.get(`node/${result.uid}`);

            if (response.ok) {
                data.doc = response.data.data as Document;
            }
        }
    }
</script>

<Node bind:node={data} {save} />
