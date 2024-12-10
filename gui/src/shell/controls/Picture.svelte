<script lang="ts">
    import type { FileData } from '$types/data';
    import type { ImageField } from '$types/fields';
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import Upload from '$shell/Upload.svelte';
    import LabelDiv from '$shell/LabelDiv.svelte';

    interface Props {
        field: ImageField;
        data: FileData;
        node: string;
    }

    let { field, data = $bindable(), node }: Props = $props();

    let lang = $state($system.locale);
</script>

<Field required={field.required}>
    <LabelDiv
        translate={field.translate}
        bind:lang>
        {field.label}
    </LabelDiv>
    <div class="mt-2">
        <Upload
            type="image"
            multiple={true}
            path="{$system.prefix}/media/image/node/{node}"
            name={field.name}
            translate={field.translateFile ? false : field.translate}
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
