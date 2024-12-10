<script lang="ts">
    import type { FileItem } from '$types/data';
    import { ModalHeader, ModalBody, ModalFooter } from '$shell/modal';
    import { _ } from '$lib/locale';
    import Button from '$shell/Button.svelte';
    import Input from '$shell/controls/Input.svelte';

    interface Props {
        close: () => void;
        apply: (asset: FileItem) => void;
        asset: FileItem;
        translate: boolean;
        hasAlt: boolean;
    }

    let { close, apply, asset = $bindable(), translate, hasAlt }: Props = $props();
</script>

<ModalHeader>{_('Bildtitel und Alt-Text')}</ModalHeader>
<ModalBody>
    <div class="flex flex-col gap-4 mb-8">
        <Input
            bind:value={asset.title}
            label={_('Titel')}
            id="edit_image_title"
            {translate} />
        {#if hasAlt}
            <Input
                bind:value={asset.alt}
                label={_('Alt-Text')}
                id="edit_image_alt"
                {translate}
                description={_(
                    'Ein Alt-Text ist eine kurze Bildbeschreibung oder eine sprachliche Übersetzung eines visuellen Inhalts im Internet, die blinden Benutzern von Hilfsmitteln wie Screen- readern anstelle des Bildes vorgelesen wird. Suchmaschinen verwenden diesen Text ebenfalls.',
                )} />
        {/if}
    </div>
</ModalBody>
<ModalFooter>
    <div class="controls">
        <Button
            class="danger"
            onclick={close}>
            {_('Abbrechen')}
        </Button>
        <Button
            class="primary"
            onclick={() => apply(asset)}>
            {_('Übernehmen')}
        </Button>
    </div>
</ModalFooter>
