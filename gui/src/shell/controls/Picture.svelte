<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import Upload from '$shell/Upload.svelte';
    import LocaleTabs from '$shell/LocaleTabs.svelte';
    import type { TextData } from '$types/data';
    import type { TextField } from '$types/fields';

    export let field: TextField;
    export let data: TextData;

    let lang = $system.locale;
</script>

<Field required={field.required}>
    <label for={field.name}>
        {field.label}
        {#if field.translate}
            <LocaleTabs {lang} />
        {/if}
    </label>
    {#if field.translate}
        <div class="mt-2">
            {#each $system.locales as locale}
                {#if locale.id === lang}
                    <Upload
                        image
                        url="/assets/{doc.assetPath}/{doc.details.uid}"
                        name="image_{content.content}"
                        bind:asset={content.data.path} />
                {/if}
            {/each}
        </div>
    {:else}
        {field.type}
    {/if}
</Field>
