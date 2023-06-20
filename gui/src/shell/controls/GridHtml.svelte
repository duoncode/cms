<script lang="ts">
    import Wysiwyg from '$shell/Wysiwyg.svelte';
    import type { GridHtml } from '$types/data';
    import type { GridField } from '$types/fields';

    export let field: GridField;
    export let item: GridHtml;
    export let index: number;
</script>

<Wysiwyg
    required={false}
    name={field.name + '_' + index}
    bind:value={item.value} />
