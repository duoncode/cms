<script lang="ts">
    import { createBubbler } from 'svelte/legacy';

    const bubble = createBubbler();
    interface Props {
        children?: import('svelte').Snippet;
    }

    let { children }: Props = $props();
</script>

<button
    class="block px-4 py-2 text-sm"
    role="menuitem"
    tabindex="-1"
    onclick={bubble('click')}>
    {@render children?.()}
</button>
