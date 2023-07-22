<script lang="ts">
    import type { Node as NodeType } from '$types/data';
    import Node from '$shell/Node.svelte';

    export let data: NodeType;
</script>

<Node bind:node={data}/>
