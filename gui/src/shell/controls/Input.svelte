<script lang="ts">
    import Field from '$shell/Field.svelte';
    import Label from '$shell/Label.svelte';
    import { system } from '$lib/sys';

    export let value: string | Record<string, string>;
    export let label: string;
    export let id: string;
    export let required = false;
    export let translate = false;
    export let description = '';

    let lang = $system.locale;
</script>

<Field {required}>
    <Label
        of={id}
        {translate}
        bind:lang>
        {label}
    </Label>
    <div class="mt-2">
        {#if translate}
            {#each $system.locales as locale}
                {#if locale.id === lang}
                    <input
                        {id}
                        name={id}
                        type="text"
                        {required}
                        autocomplete="off"
                        bind:value={value[locale.id]} />
                {/if}
            {/each}
        {:else}
            <input
                {id}
                name={id}
                type="text"
                {required}
                autocomplete="off"
                bind:value />
        {/if}
    </div>
    {#if description}
        <div class="text-sm mt-1 text-gray-400">
            {description}
        </div>
    {/if}
</Field>
