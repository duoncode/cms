<script lang="ts">
    import { _ } from '$lib/locale';
    import Button from '$shell/Button.svelte';

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
    h2 {
        @apply font-medium mb-4;
    }
</style>

<div class="modal">
    <h2>{_('Inhaltstyp hinzufügen')}</h2>
    <div class="body">
        <div class="grid grid-cols-2 gap-4 mb-8">
            {#if types.length > 0}
                {#each types as t}
                    <Button
                        class="border-sky-800 {t.id === type
                            ? 'bg-sky-800 text-white'
                            : 'text-sky-800 bg-white'}"
                        on:click={setType(t.id)}>
                        <span class="ml-2">
                            {t.label}
                        </span>
                    </Button>
                {/each}
            {/if}
        </div>
    </div>
    <div class="controls">
        <Button class="danger" on:click={close}>{_('Abbrechen')}</Button>
        <Button class="primary" on:click={addContent(true)} {disabled}>
            {index === null ? _('Einfügen') : _('Davor einfügen')}
        </Button>
        {#if index !== null}
            <Button class="primary" on:click={addContent(false)} {disabled}>
                {_('Danach einfügen')}
            </Button>
        {/if}
    </div>
</div>
