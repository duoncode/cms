<script lang="ts">
    import { toggleNav } from '../lib/ui';
    import IcoBarsCenterLeft from '$shell/icons/IcoBarsCenterLeft.svelte';
</script>

<button
    class="nav"
    onclick={toggleNav}>
    <IcoBarsCenterLeft />
</button>

<style lang="postcss">
    button.nav {
        @apply p-2 bg-white w-9 h-9;
        position: absolute;
        left: var(--s-2);
        border: 0;
        box-shadow: none;
        cursor: pointer;
    }
</style>
