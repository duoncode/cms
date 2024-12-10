<script lang="ts">
    

    interface Props {
        required?: boolean;
        class?: any;
        children?: import('svelte').Snippet;
    }

    let { required = false, class: cssclass = null, children }: Props = $props();
</script>

<div
    class="field {cssclass}"
    class:required>
    {@render children?.()}
</div>
