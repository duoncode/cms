<script lang="ts">
    import Wysiwyg from '$shell/Wysiwyg.svelte';
    import type { GridHtml } from '$types/data';
    import type { GridField } from '$types/fields';

    export let field: GridField;
    export let item: GridHtml;
    export let index: number;

console.log(item);
console.log(field);

    let showSettings = false;
</script>

<slot edit={() => showSettings = !showSettings} />

{#if showSettings}
    Settings
{:else}
    <Wysiwyg
        required={false}
        name={field.name + '_' + index}
        bind:value={item.value} />
{/if}
