<script lang="ts">
    import type { Snippet } from 'svelte';

    import { base } from '$app/paths';

    type Props = {
        class?: string;
        href: string;
        children: Snippet;
        target?: string;
    };

    let { class: cls = '', href, children, target = '' }: Props = $props();
</script>

<a
    href="{base}/{href}"
    class={cls}
    {target}>
    {@render children()}
</a>
