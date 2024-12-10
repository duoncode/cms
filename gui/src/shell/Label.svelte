<script lang="ts">
    import type { Snippet } from 'svelte';

    import LocaleTabs from '$shell/LocaleTabs.svelte';

    type Props = {
        of: string;
        translate?: boolean;
        lang?: string | null;
        children: Snippet;
    };

    let { of, translate = false, lang = $bindable(null), children }: Props = $props();
</script>

<label
    for={of}
    class="flex flex-row">
    <div class="flex-grow">{@render children()}</div>
    {#if translate}
        <LocaleTabs bind:lang />
    {/if}
</label>
