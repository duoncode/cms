<script lang="ts">
    import Wysiwyg from '$shell/Wysiwyg.svelte';
    import type { GridImage } from '$types/data';
    import type { GridField } from '$types/fields';

    export let field: GridField;
    export let data: GridImage;
    export let node: string;
    export let index: number;
</script>

<div class="col-span-{data.colspan} row-span-{data.rowspan}">
    <Wysiwyg
        required={false}
        name={field.name + '_' + index}
        bind:value={data.value} />
</div>
