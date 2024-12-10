<script lang="ts">
    import Published from '$shell/Published.svelte';

    interface Props {
        showPublished?: boolean;
        published?: boolean;
        children?: import('svelte').Snippet;
    }

    let { showPublished = false, published = false, children }: Props = $props();
</script>

<h1 class="text-3xl font-semibold mb-6 flex flex-row items-center justify-start">
    {#if showPublished}
        <span class="ml-1 inline-flex items-center">
            <Published
                {published}
                large />
        </span>
    {/if}
    <span
        class="flex items-center"
        class:pl-3={showPublished}>
        {@render children?.()}
    </span>
</h1>

<style lang="postcss">
    h1 {
        line-height: 36px;
    }
</style>
