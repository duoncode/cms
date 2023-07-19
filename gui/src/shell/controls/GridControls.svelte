<script lang="ts">
    import { createEventDispatcher } from 'svelte';
    import type { GridItem } from '$types/data';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoArrowUp from '$shell/icons/IcoArrowUp.svelte';
    import IcoExpand from '$shell/icons/IcoExpand.svelte';
    import IcoCollapse from '$shell/icons/IcoCollapse.svelte';
    import IcoIndent from '$shell/icons/IcoIndent.svelte';
    import IcoUnindent from '$shell/icons/IcoUnindent.svelte';
    import IcoArrowDown from '$shell/icons/IcoArrowDown.svelte';
    import IcoCirclePlus from '$shell/icons/IcoCirclePlus.svelte';
    import IcoThreeDots from '$shell/icons/IcoThreeDots.svelte';

    import { setDirty } from '$lib/state';

    const dispatch = createEventDispatcher();

    export let data: GridItem[];
    export let item: GridItem;
    export let index: number;
    export let edit: () => void;

    let first = false;
    let last = false;
    let widest = false;
    let narrowest = false;
    let highest = false;
    let onerow = false;
    let unindented = false;
    let fullyindented = false;

    function remove() {
        data.splice(index, 1);
        data = data;
    }

    function add() {
        dispatch('addcontent', { data, item });
    }

    function up() {
        if (first) {
            return;
        }

        data.splice(index - 1, 0, data.splice(index, 1)[0]);
        data = data;
        setDirty();
    }

    function down() {
        if (last) {
            return;
        }

        data.splice(index + 1, 0, data.splice(index, 1)[0]);
        data = data;
        setDirty();
    }

    function width(val: number) {
        return () => item.colspan = item.colspan + val;
    }

    function height(val: number) {
        return () => item.rowspan = item.rowspan + val;
    }

    function indent(val: number) {
        return () => {
            console.log(item.colstart);
            if (item.colstart === null) {
                item.colstart = 2;
            } else {
                item.colstart = item.colstart + val;
            }
        }
    }

    const dirty = () => {
        setDirty();
    };

    $: first = data.indexOf(item) === 0;
    $: last = data.indexOf(item) === data.length - 1;
</script>

<div class="content-actions flex flex-row items-center justify-end">
    <div class="flex flex-row flex-grow items-center justify-start">
        <button class="width-plus" disabled={widest} on:click={width(1)}>
            <IcoExpand />
        </button>
        <button class="width-minus" disabled={narrowest} on:click={width(-1)}>
            <IcoCollapse />
        </button>
        <button class="indent" disabled={unindented} on:click={indent(1)}>
            <IcoIndent />
        </button>
        <button class="unindent" disabled={fullyindented} on:click={indent(-1)}>
            <IcoUnindent />
        </button>
        <button class="height-plus" disabled={highest} on:click={height(1)}>
            <IcoExpand />
        </button>
        <button class="height-minus" disabled={onerow} on:click={height(-1)}>
            <IcoCollapse />
        </button>
    </div>
    <div class="flex flex-row items-center justify-end">
        <button class="up-down" disabled={last} on:click={down}>
            <IcoArrowDown />
        </button>
        <button class="up-down" disabled={first} on:click={up}>
            <IcoArrowUp />
        </button>
        <button class="add" on:click={add}>
            <IcoCirclePlus />
        </button>
        <button class="remove" on:click={remove}>
            <IcoTrash />
        </button>
        <button class="edit" on:click={edit}>
            <IcoThreeDots />
        </button>
    </div>
</div>

<style lang="postcss">
    .content-actions {
        & > div {
            @apply ml-4 py-2 gap-x-3;

            &:hover button {
                opacity: 1;
            }
        }
    }

    button {
        @apply w-4 h-4;

        transition: opacity 0.35s ease;

        &[disabled].up-down {
            @apply text-gray-300;
        }
    }

    .up-down {
        opacity: 0.2;
    }

    .remove {
        @apply text-orange-700;
        opacity: 0.2;
    }

    .add {
        @apply text-sky-700;
        opacity: 0.2;
    }

    .width-minus, .width-plus {
        transform: rotate(90deg);
    }
</style>
