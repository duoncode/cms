<script>
    import { _ } from '$lib/locale';
    import { currentDocument as doc, currentFields as fields } from '$lib/state';
    import IcoDocument from '$shell/icons/IcoDocument.svelte';
    import IcoImage from '$shell/icons/IcoImage.svelte';
    import IcoLink from '$shell/icons/IcoLink.svelte';
    import File from './ModalLinkFile.svelte';
    import Image from './ModalLinkImage.svelte';
    import Button from '$shell/Button.svelte';

    export let close;
    export let add;
    export let value;
    export let blank;

    let currentTab = 'manually';

    function clickAdd() {
        if (value) {
            add(value, blank);
            close();
        }
    }

    function clickFile(event) {
        value = event.detail.file;
    }

    function changeTab(tab) {
        return () => (currentTab = tab);
    }
</script>

<div class="modal flex flex-col">
    <h2 class="text-xl font-bold">{_('Add link')}</h2>
    <div class="flex flex-col gap-4">
        <div class="tabs">
            <div class="border-b border-gray-200">
                <nav aria-label="Tabs">
                    <button
                        class="tab"
                        class:active={currentTab === 'manually'}
                        on:click={changeTab('manually')}>
                        <IcoLink />
                        <span>{_('Manueller Link')}</span>
                    </button>
                    <button
                        class="tab"
                        class:active={currentTab === 'images'}
                        on:click={changeTab('images')}>
                        <IcoImage />
                        <span>{_('Bilder')}</span>
                    </button>
                    <button
                        class="tab"
                        class:active={currentTab === 'files'}
                        on:click={changeTab('files')}>
                        <IcoDocument />
                        <span>{_('Dateien/Dokumente')}</span>
                    </button>
                </nav>
            </div>
        </div>
        <div class="files overflow-y-auto">
            {#if currentTab === 'images'}
                {#if $fields}
                    <div class="flex flex-row flex-wrap gap-2">
                        {#each $fields as field (field)}
                            {#if field.type === 'Conia\\Cms\\Field\\Image'}
                                {#if $doc.content[field.name] && $doc.content[field.name].files}
                                    {#each $doc.content[field.name].files as file}
                                        {#if file.file}
                                            <Image
                                                node={$doc.uid}
                                                file={file.file}
                                                on:click={clickFile}
                                                bind:current={value} />
                                        {/if}
                                    {/each}
                                {/if}
                            {/if}
                        {/each}
                    </div>
                {/if}
            {:else if currentTab === 'files'}
                {#if $fields}
                    <div>
                        {#each $fields as field (field)}
                            {#if field.type === 'Conia\\Cms\\Field\\File'}
                                {#if $doc.content[field.name] && $doc.content[field.name].files}
                                    {#each $doc.content[field.name].files as file}
                                        {#if file.file}
                                            <File
                                                node={$doc.uid}
                                                file={file.file}
                                                on:click={clickFile}
                                                bind:current={value} />
                                        {/if}
                                    {/each}
                                {/if}
                            {/if}
                        {/each}
                    </div>
                {/if}
            {:else}
                <div>
                    <div class="mt-4">
                        {_('Bitte eine gültige URL eingeben')}
                    </div>
                    <div class="mt-4">
                        <input
                            class="form-input"
                            type="text"
                            bind:value />
                    </div>
                </div>
            {/if}
        </div>
    </div>
    <div class="mt-4">
        <div class="relative flex items-start">
            <div class="flex h-6 items-center">
                <input
                    id="modallink_target"
                    aria-describedby="comments-description"
                    name="modallink_target"
                    type="checkbox"
                    bind:checked={blank}
                    class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-600" />
            </div>
            <div class="ml-3 text-sm leading-6">
                <label
                    for="modallink_target"
                    class="font-semibold text-gray-900">
                    {_('In neuem Fenster öffnen')}
                </label>
            </div>
        </div>
    </div>
    <div class="controls">
        <Button
            class="danger"
            on:click={close}>
            {_('Abbrechen')}
        </Button>
        <Button
            class="primary"
            on:click={clickAdd}
            disable={!value}>
            {_('Link hinzufügen')}
        </Button>
    </div>
</div>

<style lang="postcss">
    .files {
        max-height: 50vh;
    }
</style>
