<script lang="ts">
    import { run } from 'svelte/legacy';

    import type { Modal } from 'svelte-simple-modal';
    import type { GridItem } from '$types/data';
    import { getContext } from 'svelte';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoArrowUp from '$shell/icons/IcoArrowUp.svelte';
    import IcoArrowDown from '$shell/icons/IcoArrowDown.svelte';
    import IcoCirclePlus from '$shell/icons/IcoCirclePlus.svelte';
    import ModalRemove from '$shell/modals/ModalRemove.svelte';
    import { setDirty } from '$lib/state';

    interface Props {
        data: GridItem[];
        item: GridItem;
        index: number;
        add: () => void;
        dropdown?: boolean;
    }

    let {
        data = $bindable(),
        item,
        index,
        add,
        dropdown = false
    }: Props = $props();

    const modal: Modal = getContext('simple-modal');

    let first = $state(false);
    let last = $state(false);

    async function remove() {
        modal.open(
            ModalRemove,
            {
                close: modal.close,
                proceed: () => {
                    data.splice(index, 1);
                    data = data;
                    modal.close();
                },
            },
            {
                closeButton: false,
            },
        );
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
    run(() => {
        first = data.indexOf(item) === 0;
    });
    run(() => {
        last = data.indexOf(item) === data.length - 1;
    });
</script>

<div
    class="flex flex-row flex-grow items-center py-2 gap-x-3"
    class:justify-end={!dropdown}
    class:mr-3={!dropdown}
    class:justify-center={dropdown}>
    <button
        class="remove"
        onclick={remove}>
        <IcoTrash />
    </button>
    <button
        class="up-down"
        disabled={last}
        onclick={down}>
        <IcoArrowDown />
    </button>
    <button
        class="up-down"
        disabled={first}
        onclick={up}>
        <IcoArrowUp />
    </button>
    <button
        class="add"
        onclick={add}>
        <IcoCirclePlus />
    </button>
</div>

<style lang="postcss">
    div button {
        @apply w-4 h-4;

        &[disabled] {
            @apply text-gray-300;
        }
    }

    .remove {
        @apply text-orange-700;
    }

    .add {
        @apply text-sky-700;
    }
</style>
