<script lang="ts">
    import type { Snippet } from 'svelte';

    type Props = {
        required?: boolean;
        class?: any;
        children: Snippet;
    };

    let { required = false, class: cssclass = null, children }: Props = $props();
</script>

<div
    class="field {cssclass}"
    class:required>
    {@render children()}
</div>
