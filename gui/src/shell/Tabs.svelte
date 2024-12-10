<script lang="ts">
    interface Props {
        children?: import('svelte').Snippet;
    }

    let { children }: Props = $props();
</script>

<div class="mb-6">
    <div class="border-b border-gray-200">
        <nav
            class="-mb-px flex space-x-8"
            aria-label="Tabs">
            {@render children?.()}
        </nav>
    </div>
</div>
