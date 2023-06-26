<script lang="ts">
    import type { Blueprint } from '$types/data';
    import { base } from '$app/paths';
    import { goto } from '$app/navigation';
    import { _ } from '$lib/locale';
    import Button from '$shell/Button.svelte';

    export let close: () => void;
    export let collectionSlug: string;
    export let blueprints: Blueprint[];

    function createNode(slug: string) {
        return () => {
            goto(`${base}/collection/${collectionSlug}/create/${slug}`);
            close();
        };
    }
</script>

<div class="modal">
    <h2>{_('Inhaltstyp hinzufügen')}</h2>
    <div class="body">
        <div class="grid grid-cols-2 gap-4 mb-8">
            {#if blueprints.length > 0}
                {#each blueprints as blueprint}
                    <Button
                        class="secondary"
                        on:click={createNode(blueprint.slug)}>
                        <span class="ml-2">
                            {blueprint.name}
                        </span>
                    </Button>
                {/each}
            {/if}
        </div>
    </div>
    <div class="controls">
        <Button class="danger" on:click={close}>{_('Abbrechen')}</Button>
    </div>
</div>
