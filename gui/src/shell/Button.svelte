<script lang="ts">
    import { createBubbler } from 'svelte/legacy';

    const bubble = createBubbler();
    import type { ComponentType } from 'svelte';

    
    interface Props {
        class?: string;
        icon?: ComponentType;
        disabled?: boolean;
        type?: 'submit' | 'button' | 'reset';
        small?: boolean;
        children?: import('svelte').Snippet;
    }

    let {
        class: cls = 'primary',
        icon = null,
        disabled = false,
        type = 'button',
        small = false,
        children
    }: Props = $props();
</script>

<button
    class="{cls} {small
        ? 'px-3 py-2 gap-x-1.5'
        : 'px-3.5 py-2.5 gap-x-2'} inline-flex items-center justify-center rounded-md text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
    {type}
    onclick={bubble('click')}
    onmouseover={bubble('mouseover')}
    onmouseenter={bubble('mouseenter')}
    onmouseleave={bubble('mouseleave')}
    onfocus={bubble('focus')}
    {disabled}>
    {#if icon}
        {@const SvelteComponent = icon}
        <span class="-ml-0.5 h-5 w-5">
            <SvelteComponent />
        </span>
    {/if}
    {@render children?.()}
</button>
