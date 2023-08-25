<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import Wysiwyg from '$shell/Wysiwyg.svelte';
    import LabelDiv from '$shell/LabelDiv.svelte';
    import type { TextData } from '$types/data';
    import type { SimpleField } from '$types/fields';

    export let field: SimpleField;
    export let data: TextData;

    let lang = $system.locale;
</script>

<Field required={field.required}>
    <LabelDiv
        translate={field.translate}
        bind:lang>
        {field.label}
    </LabelDiv>
    <div class="mt-2">
        {#if field.translate}
            {#each $system.locales as locale}
                {#if locale.id === lang}
                    <Wysiwyg
                        name={field.name}
                        required={field.required}
                        bind:value={data.value[locale.id]} />
                {/if}
            {/each}
        {:else}
            <Wysiwyg
                name={field.name}
                required={field.required}
                bind:value={data.value} />
        {/if}
    </div>
</Field>
