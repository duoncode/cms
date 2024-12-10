<script lang="ts">
    import type { Snippet } from 'svelte';
    import type { HTMLButtonAttributes } from 'svelte/elements';
    import type { Component } from 'svelte';

    type Props = {
        class?: string;
        icon?: Component;
        disabled?: boolean;
        type?: 'submit' | 'button' | 'reset';
        small?: boolean;
        children: Snippet;
    };

    let {
        class: cls = 'primary',
        icon = null,
        disabled = false,
        type = 'button',
        small = false,
        children,
        ...attributes
    }: Props & HTMLButtonAttributes = $props();
</script>

<button
    class="{cls} {small
        ? 'px-3 py-2 gap-x-1.5'
        : 'px-3.5 py-2.5 gap-x-2'} inline-flex items-center justify-center rounded-md text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
    {type}
    {...attributes}
    {disabled}>
    {#if icon}
        {@const Icon = icon}
        <span class="-ml-0.5 h-5 w-5">
            <Icon />
        </span>
    {/if}
    {@render children()}
</button>
