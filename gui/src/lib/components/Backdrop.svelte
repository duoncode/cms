<script>
    import { fade } from 'svelte/transition';
    import { navVisible } from '../lib/ui';
</script>

<style type="postcss">
    #backdrop {
        position: fixed;
        width: 100%;
        height: 100vh;
        background-color: rgb(75 85 99 / 0.75);
    }

    @media (--lg) {
        #backdrop {
            display: none;
        }
    }
</style>

{#if $navVisible}
    <div id="backdrop" in:fade out:fade />
{/if}
