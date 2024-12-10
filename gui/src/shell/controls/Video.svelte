<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import Upload from '$shell/Upload.svelte';
    import LabelDiv from '$shell/LabelDiv.svelte';
    import type { FileData } from '$types/data';
    import type { ImageField } from '$types/fields';

    interface Props {
        field: ImageField;
        data: FileData;
        node: string;
    }

    let { field, data = $bindable(), node }: Props = $props();

    let lang = $state($system.locale);
</script>

<Field
    required={field.required}
    class="flex flex-col h-full">
    <LabelDiv
        translate={field.translate}
        bind:lang>
        {field.label}
    </LabelDiv>
    <div class="mt-2 flex-grow">
        {#if field.translateFile}
            {#each $system.locales as locale}
                {#if locale.id === lang}
                    <Upload
                        type="video"
                        multiple={field.multiple}
                        path="{$system.prefix}/media/video/node/{node}"
                        required={field.required}
                        name={field.name}
                        translate={false}
                        bind:assets={data.files[locale.id]} />
                {/if}
            {/each}
        {:else}
            <Upload
                type="video"
                multiple={field.multiple}
                path="{$system.prefix}/media/video/node/{node}"
                required={field.required}
                name={field.name}
                translate={field.translate}
                bind:assets={data.files} />
        {/if}
    </div>
</Field>
