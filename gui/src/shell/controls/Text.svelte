<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import Label from '$shell/Label.svelte';
    import type { TextData } from '$types/data';
    import type { SimpleField } from '$types/fields';

    export let field: SimpleField;
    export let data: TextData;

    let lang = $system.locale;
</script>

<Field required={field.required}>
    <Label
        of={field.name}
        translate={field.translate}
        bind:lang>
        {field.label}
    </Label>
    <div class="mt-2">
        {#if field.translate}
            {#each $system.locales as locale}
                {#if locale.id === lang}
                    <input
                        id={field.name}
                        name={field.name}
                        type="text"
                        required={field.required}
                        bind:value={data.value[locale.id]} />
                {/if}
            {/each}
        {:else}
            <input
                id={field.name}
                name={field.name}
                type="text"
                required={field.required}
                bind:value={data.value} />
        {/if}
    </div>
</Field>
