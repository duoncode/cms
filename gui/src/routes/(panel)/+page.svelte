<script lang="ts">
    import { fade } from 'svelte/transition';
    import NavLogo from '$shell/NavLogo.svelte';

    export let data;
</script>

<style lang="postcss">
    .logo-wrapper {
        margin-top: 28vh;
    }
</style>

{#key data.pathname}
    <div
        class="logo-wrapper max-w-xl mx-auto"
        in:fade={{ duration: 300, delay: 400 }}
        out:fade={{ duration: 300 }}>
        <NavLogo />
    </div>
{/key}
