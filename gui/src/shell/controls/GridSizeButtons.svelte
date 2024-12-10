<script lang="ts">
    import { run } from 'svelte/legacy';

    import type { GridItem } from '$types/data';
    import type { GridField } from '$types/fields';
    import GridButtonLabel from '$shell/controls/GridButtonLabel.svelte';
    import IcoExpand from '$shell/icons/IcoExpand.svelte';
    import IcoCollapse from '$shell/icons/IcoCollapse.svelte';
    import IcoIndent from '$shell/icons/IcoIndent.svelte';
    import IcoUnindent from '$shell/icons/IcoUnindent.svelte';

    interface Props {
        item: GridItem;
        field: GridField;
        dropdown?: boolean;
    }

    let { item = $bindable(), field = $bindable(), dropdown = false }: Props = $props();

    let widest = $state(false);
    let narrowest = $state(false);
    let highest = $state(false);
    let onerow = $state(false);
    let unindented = $state(false);
    let fullyindented = $state(false);

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

    run(() => {
        widest = item.colspan === field.columns;
    });
    run(() => {
        narrowest = item.colspan === field.minCellWidth;
    });
    run(() => {
        highest = item.rowspan === field.columns * 2;
    }); // This is arbitrary. Allow twice as many rows as columns
    run(() => {
        onerow = item.rowspan === 1;
    });
    run(() => {
        unindented = item.colstart === null;
    });
    run(() => {
        fullyindented =
            item.colstart !== null && item.colstart + item.colspan - 1 === field.columns;
    });
</script>

<div
    class="flex flex-row flex-grow items-center py-2 gap-x-3"
    class:justify-start={!dropdown}
    class:justify-center={dropdown}>
    <button
        class="width-plus"
        disabled={widest}
        onclick={width(1)}>
        <span class="icon">
            <IcoExpand />
        </span>
        <GridButtonLabel value={item.colspan} />
    </button>
    <button
        class="width-minus"
        disabled={narrowest}
        onclick={width(-1)}>
        <span class="icon">
            <IcoCollapse />
        </span>
        <GridButtonLabel value={item.colspan} />
    </button>
    <button
        class="indent"
        disabled={fullyindented}
        onclick={indent(1)}>
        <IcoIndent />
        <GridButtonLabel value={item.colstart} />
    </button>
    <button
        class="unindent"
        disabled={unindented}
        onclick={indent(-1)}>
        <IcoUnindent />
        <GridButtonLabel value={item.colstart} />
    </button>
    <button
        class="height-plus"
        disabled={highest}
        onclick={height(1)}>
        <IcoExpand />
        <GridButtonLabel value={item.rowspan} />
    </button>
    <button
        class="height-minus"
        disabled={onerow}
        onclick={height(-1)}>
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
