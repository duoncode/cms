<script lang="ts">
    import { createEventDispatcher } from 'svelte';
    import type { GridItem } from '$types/data';
    import type { GridField } from '$types/fields';
    import GridSizeButtons from '$shell/controls/GridSizeButtons.svelte';
    import GridCellButtons from '$shell/controls/GridCellButtons.svelte';
    import IcoThreeDots from '$shell/icons/IcoThreeDots.svelte';
    import IcoGear from '$shell/icons/IcoGear.svelte';

    const dispatch = createEventDispatcher();

    interface Props {
        data: GridItem[];
        item: GridItem;
        field: GridField;
        index: number;
        edit: () => void;
    }

    let {
        data = $bindable(),
        item = $bindable(),
        field = $bindable(),
        index = $bindable(),
        edit
    }: Props = $props();

    let showDropdown = $state(false);

    function add() {
        dispatch('addcontent', { data, item });
    }
</script>

<div class="content-actions flex flex-row items-center justify-end">
    {#if item.width < 350}
        <div class="flex flex-row flex-grow items-center justify-end py-2 gap-x-3 mr-3">
            <div class="grid-buttons dropdown relative inline-block text-left">
                <div>
                    <button
                        type="button"
                        class="flex items-center"
                        onclick={() => (showDropdown = !showDropdown)}>
                        <span class="sr-only">Open options</span>
                        <IcoThreeDots />
                    </button>
                </div>
                {#if showDropdown}
                    <div
                        class="absolute right-0 z-10 mt-2 w-44 px-2 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu"
                        aria-orientation="vertical"
                        aria-labelledby="menu-button"
                        tabindex="-1">
                        <div
                            class="py-1 flex flex-col justify-center"
                            role="none">
                            <GridCellButtons
                                bind:data
                                bind:item
                                bind:index
                                {add}
                                dropdown />
                            <GridSizeButtons
                                bind:field
                                bind:item
                                dropdown />
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    {:else}
        <div class="grid-buttons flex flex-row flex-grow items-center justify-end">
            <GridSizeButtons
                bind:field
                bind:item />
            <GridCellButtons
                bind:data
                bind:item
                bind:index
                {add} />
        </div>
    {/if}
    <div class="flex flex-row flex-shrink items-center justify-end">
        <button
            class="edit"
            onclick={edit}>
            <IcoGear />
        </button>
    </div>
</div>

<style lang="postcss">
    div button {
        @apply w-4 h-4;
    }

    .grid-buttons {
        opacity: 0;
        transition: opacity 0.35s ease;

        &.dropdown {
            opacity: 1;
        }

        &:hover {
            opacity: 1;
        }

        :global(button .grid-button-label) {
            opacity: 0;
        }
        :global(button:hover .grid-button-label) {
            opacity: 1;
        }
    }

    .edit {
        opacity: 1;
    }
</style>
