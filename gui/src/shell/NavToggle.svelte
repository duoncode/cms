<script lang="ts">
    import { toggleNav } from '../lib/ui';
</script>

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

<button class="nav" on:click={toggleNav}>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor">
        <path
            fill-rule="evenodd"
            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
            clip-rule="evenodd" />
    </svg>
</button>
