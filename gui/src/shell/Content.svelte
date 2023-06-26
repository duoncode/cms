<script lang="ts">
    import type { Document } from '$types/data';
    import type { Field } from '$types/fields';
    import controls from '$lib/controls';

    export let doc: Document;
    export let fields: Field[];

    function fieldSpan(value: number | null) {
        if (value) {
            if (value > 100 || value <= 0) value = 100;

            return `span ${value} / span ${value}`;
        }

        return 'span 100 / span 100';
    }
</script>

<style lang="postcss">
    .field-grid {
        display: grid;
        grid-template-columns: repeat(100, minmax(0, 1fr));
    }
</style>

<div class="field-grid">
    {#each fields as field}
        <div
            style="
                grid-column: {fieldSpan(field.width)};
                grid-row: {fieldSpan(field.rows)}">
            {#if controls[field.type]}
                <svelte:component
                    this={controls[field.type]}
                    {field}
                    node={doc.uid}
                    bind:data={doc.content[field.name]} />
            {:else}
                {field.type}
            {/if}
        </div>
    {/each}
</div>
