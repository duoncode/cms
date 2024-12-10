<script lang="ts">
    import { fade } from 'svelte/transition';

    let { data, children } = $props();
</script>

{#key data.pathname}
    <div
        in:fade={{ duration: 300, delay: 100 }}
        out:fade={{ duration: 300 }}>
        {@render children?.()}
    </div>
{/key}

<style lang="postcss">
    div {
        height: 100vh;
        width: 100%;
        overflow: hidden;
    }
</style>
