<script lang="ts">
    import type { Snippet } from 'svelte';

    type Props = {
        children: Snippet;
    };

    let { children }: Props = $props();
</script>

<div class="flex-1">
    <div class="max-w-7xl pt-2 pb-4 bg-white border border-gray-200 mb-12 shadow mx-auto">
        {@render children()}
    </div>
</div>
