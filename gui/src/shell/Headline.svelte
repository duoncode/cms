<script lang="ts">
    import Published from '$shell/Published.svelte';

    export let showPublished = false;
    export let published = false;
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
        <slot />
    </span>
</h1>

<style lang="postcss">
    h1 {
        line-height: 36px;
    }
</style>
