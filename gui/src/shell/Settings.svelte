<script lang="ts">
    import type { Document } from '$types/data';
    import type { Locale } from '$lib/sys';
    import { _ } from '$lib/locale';
    import { system } from '$lib/sys';
    import ToggleLine from '$shell/ToggleLine.svelte';

    export let doc: Document;

    function getPathPlaceholder(locale: Locale) {
        const localeId = locale.id;

        while (locale) {
            const value = doc.paths[locale.id];

            if (value) {
                return value;
            }

            locale = $system.locales.find(l => l.id === locale.fallback);
        }

        const value = doc.generatedPaths[localeId];

        if (value) {
            return value;
        }

        return '';
    }
</script>

<div class="p-4 sm:p-6 md:p-8">
    {#if doc.nodetype === 'page'}
        <div class="paths mb-8">
            {#each $system.locales as locale}
                <div class="path">
                    <div class="label">{locale.title}:</div>
                    <div class="value">
                        <input
                            type="text"
                            bind:value={doc.paths[locale.id]}
                            placeholder={getPathPlaceholder(locale)}
                            required={locale.id === $system.defaultLocale} />
                    </div>
                </div>
            {/each}
        </div>
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
    {/if}
</div>

<style lang="postcss">
    .paths {
        display: table;
        width: 100%;
    }

    .path {
        display: table-row;

        & > div {
            @apply p-2;
            display: table-cell;
        }

        .value {
            width: 100%;
        }
    }
</style>
