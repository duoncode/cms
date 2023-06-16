<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import Wysiwyg from '$shell/Wysiwyg.svelte';
    import Label from '$shell/Label.svelte';
    import type { TextData } from '$types/data';
    import type { TextField } from '$types/fields';

    export let field: TextField;
    export let data: TextData;

    let lang = $system.locale;
</script>

<Field required={field.required}>
    <Label of={field.name} translate={field.translate} bind:lang>
        {field.label}
    </Label>
    {#if field.translate}
        <div class="mt-2">
            {#each $system.locales as locale}
                {#if locale.id === lang}
                    <Wysiwyg
                        id={field.name}
                        name={field.name}
                        type="text"
                        required={field.required}
                        bind:value={data.value[locale.id]} />
                {/if}
            {/each}
        </div>
    {:else}
        {field.type}
    {/if}
</Field>
