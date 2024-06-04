<script lang="ts">
    import type { FileData } from '$types/data';
    import type { ImageField } from '$types/fields';
    import FileLike from './FileLike.svelte';

    export let field: ImageField;
    export let data: FileData;
    export let node: string;
</script>

<FileLike
    type="file"
    {field}
    {data}
    {node} />
