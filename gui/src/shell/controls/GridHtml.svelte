<script lang="ts">
    import Wysiwyg from '$shell/Wysiwyg.svelte';
    import type { GridHtml } from '$types/data';
    import type { GridField } from '$types/fields';

    interface Props {
        field: GridField;
        item: GridHtml;
        index: number;
        children?: import('svelte').Snippet<[any]>;
    }

    let {
        field,
        item = $bindable(),
        index,
        children
    }: Props = $props();

    let showSettings = $state(false);
</script>

<div class="grid-cell-header">
    {@render children?.({ edit: () => (showSettings = !showSettings), })}
</div>
<div class="grid-cell-body">
    {#if showSettings}
        <div>Keine Einstellungsmöglichkeiten vorhanden</div>
    {:else}
        <Wysiwyg
            required={false}
            name={field.name + '_' + index}
            bind:value={item.value} />
    {/if}
</div>
