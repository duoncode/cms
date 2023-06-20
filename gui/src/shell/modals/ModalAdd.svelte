<script lang="ts">
    import { _ } from '$lib/locale';
    import { pristine } from '$lib/state';

    export let add: (index: number, before: boolean, type: string) => void;
    export let close: () => void;
    export let index: number;
    export let types: { id: string; label: string }[];

    let type: string | null = null;
    let disabled = true;

    function addContent(before: boolean) {
        return () => {
            if (!disabled) {
                add(index, before, type);
                close();
            }
        };
    }

    function setType(t: string) {
        return () => (type = t);
    }

    $: disabled = type === null;
</script>

<style lang="postcss">
    button {
        @apply relative border border-sky-600 font-medium rounded-md;
        @apply m-2 ml-0 p-2 text-left focus:outline-none transition;
    }

    .danger {
        @apply text-white bg-orange-700 border-orange-700;
    }

    .primary {
        @apply text-white bg-sky-700 border-sky-700;
    }

    h2 {
        @apply font-medium mb-4;
    }
</style>

<div class="message">
    <h2>{_('Inhaltstyp hinzufügen')}</h2>
    <div class="body">
        <div class="grid grid-cols-2">
            {#if types.length > 0}
                {#each types as t}
                    <button
                        class=""
                        class:bg-sky-600={t.id === type}
                        class:text-sky-600={t.id !== type}
                        class:text-white={t.id === type}
                        on:click={setType(t.id)}>
                        <span class="ml-2">
                            {t.label}
                        </span>
                    </button>
                {/each}
            {/if}
        </div>
    </div>
    <div class="controls text-right">
        <button class="danger" on:click={close}>{_('Abbrechen')} </button>
        <button class="primary" on:click={addContent(true)} {disabled}>
            {index === null ? _('Einfügen') : _('Davor einfügen')}
        </button>
        {#if index !== null}
            <button class="primary" on:click={addContent(false)} {disabled}>
                {_('Danach einfügen')}
            </button>
        {/if}
    </div>
</div>
