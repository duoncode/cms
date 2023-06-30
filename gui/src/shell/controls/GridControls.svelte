<script lang="ts">
    import { createEventDispatcher } from 'svelte';
    import type { GridItem } from '$types/data';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoArrowUp from '$shell/icons/IcoArrowUp.svelte';
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

    const dirty = () => {
        setDirty();
    };

    $: first = data.indexOf(item) === 0;
    $: last = data.indexOf(item) === data.length - 1;
</script>

<div class="content-actions flex flex-row items-center justify-end">
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

<style lang="postcss">
    .content-actions {
        @apply flex-shrink flex items-center justify-end ml-4 py-2 gap-x-3;

        &:hover button {
            opacity: 1;
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
</style>
