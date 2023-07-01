<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import Upload from '$shell/Upload.svelte';
    import LabelDiv from '$shell/LabelDiv.svelte';
    import type { FileData } from '$types/data';
    import type { ImageField } from '$types/fields';

    export let field: ImageField;
    export let data: FileData;
    export let node: string;

    let lang = $system.locale;
</script>

<Field required={field.required}>
    <LabelDiv translate={field.translate} bind:lang>
        {field.label}
    </LabelDiv>
    <div class="mt-2">
        {#if field.translateFile}
            {#each $system.locales as locale}
                {#if locale.id === lang}
                    <Upload
                        image
                        path="/media/image/node/{node}"
                        name={field.name}
                        translate={field.translateFile
                            ? false
                            : field.translate}
                        bind:assets={data.files[locale.id]} />
                {/if}
            {/each}
        {:else}
            <Upload
                image
                multiple={field.multiple}
                path="/media/image/node/{node}"
                name={field.name}
                translate={field.translateFile ? false : field.translate}
                bind:assets={data.files} />
        {/if}
    </div>
</Field>
