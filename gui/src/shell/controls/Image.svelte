<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import Upload from '$shell/Upload.svelte';
    import Label from '$shell/Label.svelte';
    import type { ImageData } from '$types/data';
    import type { ImageField } from '$types/fields';

    export let field: ImageField;
    export let data: ImageData;
    export let node: string;

    let lang = $system.locale;
</script>

<Field required={field.required}>
    <Label of={field.name} translate={field.translate} bind:lang>
        {field.label}
    </Label>
    <div class="mt-2">
        {#if field.translateImage}
            {#each $system.locales as locale}
                {#if locale.id === lang}
                    <Upload
                        image
                        url="/assets/node/{node}"
                        cache="/cache/node/{node}"
                        name={field.name}
                        bind:asset={data.files[locale.id].file} />
                    <input
                        type="text"
                        name="{field.name}_alt_{locale.id}"
                        bind:value={data.files[locale.id].alt} />
                {/if}
            {/each}
        {:else}
            {#each data.files as file}
                <Upload
                    image
                    url="/assets/node/{node}"
                    cache="/cache/node/{node}"
                    name={field.name}
                    bind:asset={file.file} />
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
            {/each}
        {/if}
    </div>
</Field>
