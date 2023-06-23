<script lang="ts">
    import { base } from '$app/paths';

    let cls = '';
    let target = '';

    export { cls as class };
    export let href: string;
</script>

<a href="{base}/{href}" class={cls} {target}>
    <slot />
</a>
