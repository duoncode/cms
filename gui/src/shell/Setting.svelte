<script lang="ts">
    interface Props {
        required?: boolean;
        children?: import('svelte').Snippet;
    }

    let { required = false, children }: Props = $props();
</script>

<div
    class="setting"
    class:required>
    {@render children?.()}
</div>
