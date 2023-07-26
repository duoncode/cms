<script lang="ts">
    import Published from '$shell/Published.svelte';

    export let published: boolean;
</script>

<h1
    class="text-3xl font-semibold mb-6 flex flex-row items-center justify-start">
    <span class="ml-1 inline-flex items-center">
        <Published {published} large />
    </span>
    <span class="pl-3 flex items-center">
        <slot />
    </span>
</h1>

<style lang="postcss">
    h1 {
        line-height: 36px;
    }
</style>
