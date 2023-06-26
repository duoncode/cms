<script lang="ts">
    import type { Document } from '$types/data';
    import { _ } from '$lib/locale';
    import ToggleLine from '$shell/ToggleLine.svelte';

    export let doc: Document;
</script>

<style lang="postcss">
</style>

<div class="p-4 sm:p-6 md:p-8">
    <div class="max-w-xl">
        <div class="mb-4">
            <ToggleLine
                title={_('Veröffentlicht')}
                subtitle={_(
                    'Legt fest, ob die Seite für alle Besucher erreichbar ist.',
                )}
                bind:value={doc.published} />
        </div>
        <div class="my-4">
            <ToggleLine
                title={_('Gesperrt')}
                subtitle={_(
                    'Seiten die gesperrt sind, können nicht verändert werden.',
                )}
                bind:value={doc.locked} />
        </div>
        <div class="mt-4">
            <ToggleLine
                title={_('Versteckt')}
                subtitle={_(
                    'Versteckte Seiten werden in Auflistungen ignoriert.',
                )}
                bind:value={doc.hidden} />
        </div>
    </div>
</div>
