<script lang="ts">
    interface Props {
        children?: import('svelte').Snippet;
    }

    let { children }: Props = $props();
</script>

<div class="flex-1 flex-grow overflow-y-scroll">
    <div class="w-full max-w-7xl mx-auto px-8">
        {@render children?.()}
    </div>
</div>
