<script lang="ts">
    import LocaleTabs from '$shell/LocaleTabs.svelte';

    interface Props {
        of: string;
        translate?: boolean;
        lang?: string | null;
        children?: import('svelte').Snippet;
    }

    let {
        of,
        translate = false,
        lang = $bindable(null),
        children
    }: Props = $props();
</script>

<label
    for={of}
    class="flex flex-row">
    <div class="flex-grow">{@render children?.()}</div>
    {#if translate}
        <LocaleTabs bind:lang />
    {/if}
</label>
