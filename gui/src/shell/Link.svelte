<script lang="ts">
    import { base } from '$app/paths';

    let target = '';

    
    interface Props {
        class?: string;
        href: string;
        children?: import('svelte').Snippet;
    }

    let { class: cls = '', href, children }: Props = $props();
</script>

<a
    href="{base}/{href}"
    class={cls}
    {target}>
    {@render children?.()}
</a>
