<script lang="ts">
    import type { GridItem } from '$types/data';
    import type { GridField } from '$types/fields';
    import GridButtonLabel from '$shell/controls/GridButtonLabel.svelte';
    import IcoExpand from '$shell/icons/IcoExpand.svelte';
    import IcoCollapse from '$shell/icons/IcoCollapse.svelte';
    import IcoIndent from '$shell/icons/IcoIndent.svelte';
    import IcoUnindent from '$shell/icons/IcoUnindent.svelte';

    export let item: GridItem;
    export let field: GridField;
    export let dropdown = false;

    let widest = false;
    let narrowest = false;
    let highest = false;
    let onerow = false;
    let unindented = false;
    let fullyindented = false;

    function width(val: number) {
        return () => (item.colspan = item.colspan + val);
    }

    function height(val: number) {
        return () => (item.rowspan = item.rowspan + val);
    }

    function indent(val: number) {
        return () => {
            let colstart = item.colstart;

            if (val > 0 && colstart === null) {
                item.colstart = 2;
                return;
            }

            colstart += val;

            if (colstart === 0) {
                colstart = null;
            }

            item.colstart = colstart;
        };
    }

    $: widest = item.colspan === field.columns;
    $: narrowest = item.colspan === field.minCellWidth;
    $: highest = item.rowspan === field.columns * 2; // This is arbitrary. Allow twice as many rows as columns
    $: onerow = item.rowspan === 1;
    $: unindented = item.colstart === null;
    $: fullyindented = item.colstart !== null && item.colstart + item.colspan - 1 === field.columns;
</script>

<div
    class="flex flex-row flex-grow items-center py-2 gap-x-3"
    class:justify-start={!dropdown}
    class:justify-center={dropdown}>
    <button
        class="width-plus"
        disabled={widest}
        on:click={width(1)}>
        <span class="icon">
            <IcoExpand />
        </span>
        <GridButtonLabel value={item.colspan} />
    </button>
    <button
        class="width-minus"
        disabled={narrowest}
        on:click={width(-1)}>
        <span class="icon">
            <IcoCollapse />
        </span>
        <GridButtonLabel value={item.colspan} />
    </button>
    <button
        class="indent"
        disabled={fullyindented}
        on:click={indent(1)}>
        <IcoIndent />
        <GridButtonLabel value={item.colstart} />
    </button>
    <button
        class="unindent"
        disabled={unindented}
        on:click={indent(-1)}>
        <IcoUnindent />
        <GridButtonLabel value={item.colstart} />
    </button>
    <button
        class="height-plus"
        disabled={highest}
        on:click={height(1)}>
        <IcoExpand />
        <GridButtonLabel value={item.rowspan} />
    </button>
    <button
        class="height-minus"
        disabled={onerow}
        on:click={height(-1)}>
        <IcoCollapse />
        <GridButtonLabel value={item.rowspan} />
    </button>
</div>

<style lang="postcss">
    div button {
        @apply w-4 h-4 relative;

        &[disabled] {
            @apply text-gray-300;
        }
    }

    .width-minus,
    .width-plus {
        span.icon {
            display: block;
            transform: rotate(90deg);
        }
    }
</style>
