<script lang="ts">
    import type { ComponentType } from 'svelte';
    let cls = 'primary';
    let openMenu = false;

    function closeMenu() {
        openMenu = false;
    }

    export let icon: ComponentType = null;
    export let label: string;
    export { cls as class };
</script>

<div class="inline-flex rounded-md shadow-sm">
    <button
        type="button"
        class="{cls} px-3.5 py-2.5 gap-x-2 inline-flex items-center justify-center rounded-l-md text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus:z-10"
        on:click
        on:mouseover
        on:mouseenter
        on:mouseleave
        on:focus>
        {#if icon}
            <span class="-ml-0.5 h-5 w-5">
                <svelte:component this={icon} />
            </span>
        {/if}
        {label}
    </button>
    <div class="relative -ml-px block">
        <button
            type="button"
            class="{cls} relative inline-flex items-center rounded-r-md p-2.5 text-gray-400 border-l border-gray-300 focus:z-10"
            id="option-menu-button"
            aria-expanded="true"
            aria-haspopup="true"
            on:click={() => (openMenu = !openMenu)}>
            <span class="sr-only">Open options</span>
            <svg
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true">
                <path
                    fill-rule="evenodd"
                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                    clip-rule="evenodd" />
            </svg>
        </button>
        {#if openMenu}
            <div
                class="button-menu {cls} absolute right-0 z-10 -mr-1 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="option-menu-button"
                tabindex="-1">
                <div class="py-1" role="none">
                    <slot {closeMenu} />
                </div>
            </div>
        {/if}
    </div>
</div>
