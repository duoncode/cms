<script lang="ts">
    import Upload from '$shell/Upload.svelte';
    import type { GridImage } from '$types/data';
    import type { GridField } from '$types/fields';

    export let field: GridField;
    export let data: GridImage;
    export let node: string;
    export let index: number;
</script>

<div class="col-span-{data.colspan} row-span-{data.rowspan}">
    <Upload
        image
        multiple={false}
        url="/assets/node/{node}"
        cache="/cache/node/{node}"
        name={field.name + '_' + index}
        bind:assets={data.files} />
</div>
