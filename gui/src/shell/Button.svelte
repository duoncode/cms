<script lang="ts">
    import type { ComponentType } from 'svelte';
    let cls = 'primary';

    export let icon: ComponentType = null;
    export let disabled = false;
    export let type: 'submit' | 'button' | 'reset' = 'button';
    export { cls as class };
</script>

<style lang="postcss">
    button {
        @apply relative border font-medium rounded-md;
        @apply py-2 px-3 text-left focus:outline-none transition;
        @apply flex flex-row items-center justify-center;

        span {
            @apply inline-block w-5 h-5 mr-2;
        }
    }

    .danger {
        @apply text-white bg-rose-800 border-rose-800;
    }

    .primary {
        @apply text-white bg-emerald-800 border-emerald-800;
    }

    .secondary {
        @apply text-white bg-blue-800 border-blue-800;
    }
</style>

<button
    class={cls}
    {type}
    on:click
    on:mouseover
    on:mouseenter
    on:mouseleave
    on:focus
    {disabled}>
    {#if icon}
        <span>
            <svelte:component this={icon} />
        </span>
    {/if}
    <slot />
</button>
