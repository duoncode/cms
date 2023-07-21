<script lang="ts">
    import type { FileItem } from '$types/data';
    import { _ } from '$lib/locale';
    import Button from '$shell/Button.svelte';
    import Input from '$shell/controls/Input.svelte';

    export let close: () => void;
    export let apply: (asset: FileItem) => void;
    export let asset: FileItem;
    export let translate: boolean;
    export let hasAlt: boolean;
</script>

<div class="modal">
    <h2>{_('Bildtitel und Alt-Text')}</h2>
    <div class="body">
        <div class="flex flex-col gap-4 mb-8">
            <Input bind:value={asset.title} label={_('Titel')} id="edit_image_title" {translate}/>
            {#if hasAlt}
                <Input bind:value={asset.alt} label={_('Alt-Text')} id="edit_image_alt" {translate}
                    description={_('Ein Alt-Text ist eine kurze Bildbeschreibung oder eine sprachliche Übersetzung eines visuellen Inhalts im Internet, die blinden Benutzern von Hilfsmitteln wie Screen- readern anstelle des Bildes vorgelesen wird. Suchmaschinen verwenden diesen Text ebenfalls.')}/>
            {/if}
        </div>
    </div>
    <div class="controls">
        <Button class="danger" on:click={close}>{_('Abbrechen')}</Button>
        <Button class="primary" on:click={() => apply(asset)}>{_('Übernehmen')}</Button>
    </div>
</div>
