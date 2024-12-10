<script lang="ts">
    interface Props {
        children?: import('svelte').Snippet;
    }

    let { children }: Props = $props();
</script>

<div class="flex-1">
    <div class="max-w-7xl pt-2 pb-4 bg-white border border-gray-200 mb-12 shadow mx-auto">
        {@render children?.()}
    </div>
</div>
