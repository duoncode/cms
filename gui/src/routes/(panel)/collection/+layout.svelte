<script lang="ts">
    import { fade } from 'svelte/transition';

    export let data;
</script>

<style lang="postcss">
    div {
        height: 100vh;
        width: 100%;
        overflow: hidden;
    }
</style>

{#key data.pathname}
    <div in:fade={{ duration: 300, delay: 100 }} out:fade={{ duration: 300 }}>
        <slot />
    </div>
{/key}
