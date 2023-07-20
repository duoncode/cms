<script lang="ts">
    import type { ComponentType } from 'svelte';
    let cls = 'primary';

    export let icon: ComponentType = null;
    export let disabled = false;
    export let type: 'submit' | 'button' | 'reset' = 'button';
    export { cls as class };
    export let small = false;
</script>

<button
    class="{cls} {small
        ? 'px-3 py-2 gap-x-1.5'
        : 'px-3.5 py-2.5 gap-x-2'} inline-flex items-center justify-center rounded-md text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
    {type}
    on:click
    on:mouseover
    on:mouseenter
    on:mouseleave
    on:focus
    {disabled}>
    {#if icon}
        <span class="-ml-0.5 h-5 w-5">
            <svelte:component this={icon} />
        </span>
    {/if}
    <slot />
</button>
