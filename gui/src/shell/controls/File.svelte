<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import Upload from '$shell/Upload.svelte';
    import Label from '$shell/Label.svelte';
    import type { FileData } from '$types/data';
    import type { FileField } from '$types/fields';

    export let field: FileField;
    export let data: FileData;
    export let node: string;

    let lang = $system.locale;
</script>

<Field required={field.required}>
    <Label of={field.name} translate={field.translate} bind:lang>
        {field.label}
    </Label>
    <div class="mt-2">
        <Upload
            file
            path="/media/file/node/{node}"
            multiple={field.multiple}
            name={field.name}
            translate={field.translate}
            bind:assets={data.files} />
    </div>
</Field>
