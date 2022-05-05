<script>
    import { toggleNav } from '../lib/ui';
</script>

<style>
    button.nav {
        padding: var(--sz-2);
        margin-left: var(--sz-2);

        background-color: var(--white);
        border: 0;
        box-shadow: none;
        cursor: pointer;
    }
</style>

<button class="nav" on:click={toggleNav}>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-5 w-5"
        viewBox="0 0 20 20"
        fill="currentColor">
        <path
            fill-rule="evenodd"
            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
            clip-rule="evenodd" />
    </svg>
</button>
