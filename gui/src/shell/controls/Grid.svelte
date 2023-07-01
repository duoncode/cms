<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
    import LabelDiv from '$shell/LabelDiv.svelte';
    import GridPanel from './GridPanel.svelte';
    import type { GridData } from '$types/data';
    import type { GridField } from '$types/fields';

    export let field: GridField;
    export let data: GridData;
    export let node: string;

    let lang = $system.locale;
</script>

<Field required={field.required}>
    <LabelDiv translate={field.translate} bind:lang>
        {field.label}
    </LabelDiv>
    <div class="mt-2">
        {#if data.value}
            {#if field.translate}
                {#each $system.locales as locale}
                    {#if locale.id === lang}
                        <GridPanel
                            bind:data={data.value[lang]}
                            {field}
                            {node} />
                    {/if}
                {/each}
            {:else}
                <GridPanel bind:data={data.value} />
            {/if}
        {/if}
    </div>
</Field>
