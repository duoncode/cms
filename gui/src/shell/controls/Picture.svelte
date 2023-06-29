<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import Upload from '$shell/Upload.svelte';
    import Label from '$shell/Label.svelte';
    import type { FileData } from '$types/data';
    import type { ImageField } from '$types/fields';

    export let field: ImageField;
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
            image
            multiple={true}
            url="/assets/node/{node}"
            cache="/media/image/node/{node}"
            name={field.name}
            bind:assets={data.files} />
        <!-- As picture tags show only one image, we need only one alt definition
            {#if i === 0}
                {#if field.translate}
                    {#each $system.locales as locale}
                        {#if locale.id === lang}
                            <input
                                type="text"
                                name="{field.name}_alt_{locale.id}"
                                bind:value={file.alt[locale.id]} />
                        {/if}
                    {/each}
                {:else}
                    <input
                        type="text"
                        name="{field.name}_alt"
                        bind:value={file.alt} />
                {/if}
            {/if -->
    </div>
</Field>
