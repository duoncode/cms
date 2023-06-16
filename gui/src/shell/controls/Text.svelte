<script lang="ts">
    import { system } from '$lib/sys';
    import Field from '$shell/Field.svelte';
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
            <span class="lang-tabs">
                {#each $system.locales as locale}
                    <button
                        class="lang-tab"
                        class:active={locale.id === lang}
                        on:click={() => (lang = locale.id)}>
                        {locale.id.toUpperCase()}
                    </button>
                {/each}
            </span>
        {/if}
    </label>
    {#if field.translate}
        <div class="mt-2">
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
        </div>
    {:else}
        {field.type}
    {/if}
</Field>
