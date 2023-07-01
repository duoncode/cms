<script lang="ts">
    import type { Modal } from 'svelte-simple-modal';
    import type { FileItem, UploadResponse } from '$types/data';

    import { getContext, createEventDispatcher } from 'svelte';
    import { _ } from '$lib/locale';
    import { system } from '$lib/sys';
    import { setDirty } from '$lib/state';
    import toast from '$lib/toast';
    import req from '$lib/req.js';
    import ImageValue from '$shell/Image.svelte';
    import FileValue from '$shell/File.svelte';
    import IcoUpload from '$shell/icons/IcoUpload.svelte';
    import Dialog from '$shell/Dialog.svelte';
    import Message from '$shell/Message.svelte';

    export let path: string;
    export let image = false; // if present thumbs will be rendered
    export let file = false; // if present thumbs will be rendered
    export let name: string;
    export let translate: boolean;
    export let assets: FileItem[];
    export let label = null;
    export let multiple = false;
    export let size = 'xl';
    export let querystring = '';
    export let disabled = false;
    export let disabledMsg = null;
    export let callback = null;
    export let inline = false;

    let loading = false;
    let dragging = false;

    const dispatch = createEventDispatcher();
    const { open, close }: Modal = getContext('simple-modal');

    function remove(index: number) {
        if (index === null) {
            assets = [];
        } else {
            assets.splice(index, 1);
            assets = assets;
        }
        setDirty();
        dispatch('dirty');
    }

    function readItems(items: DataTransferItemList) {
        let result = [];

        for (const item of items) {
            if (item.kind === 'file') {
                result.push(item.getAsFile());
            }
        }

        return result;
    }

    function getFilesFromDrop({ dataTransfer: { files, items } }: DragEvent) {
        let result = files.length ? [...files] : readItems(items);

        if (!multiple && result.length > 1) {
            open(
                Dialog,
                {
                    title: _('Error'),
                    body: _('-error-upload-only-one-file-'),
                    type: 'error',
                    close,
                },
                {
                    closeButton: false,
                },
            );
            return [];
        } else {
            return result;
        }
    }

    function getFilesFromInput(event: Event) {
        const target = event.target as HTMLInputElement;
        const files = target.files ? [...target.files] : [];

        target.value = '';
        return files;
    }

    function startDragging() {
        dragging = true;
    }

    function stopDragging() {
        dragging = false;
    }

    async function upload(file: File) {
        let formData = new FormData();

        formData.append('file', file);
        return await req.post(path, formData);
    }

    function getFileName(item: UploadResponse) {
        if (item.ok) {
            return item.file;
        }

        toast.add({ kind: 'error', message: item.error });

        return null;
    }

    function getTitleAltValue() {
        if (translate) {
            const result: Record<string, string> = {};
            $system.locales.map(locale => (result[locale.id] = ''));
            return result;
        }

        return '';
    }

    function onFile(getFilesFunction: (event: DragEvent | Event) => File[]) {
        return async (event: Event) => {
            stopDragging();
            let files = getFilesFunction(event);

            if (files.length > 0) {
                loading = true;

                let responses = await Promise.all(
                    files.map(async (file: File) => {
                        return upload(file).then(resp =>
                            resp.ok ? resp.data : null,
                        );
                    }),
                );

                if (responses.length > 0) {
                    const value = getTitleAltValue();

                    if (multiple) {
                        responses.map((item: UploadResponse) => {
                            assets.push({
                                alt: value,
                                title: value,
                                file: getFileName(item),
                            });
                        });
                    } else {
                        assets = [
                            {
                                alt: value,
                                title: value,
                                file: getFileName(responses[0]),
                            },
                        ];
                    }

                    if (assets && callback) {
                        callback();
                    }
                } else {
                    console.log('TODO: error handling');
                }
            }

            loading = false;
            setDirty();
            dispatch('dirty');
        };
    }
</script>

{#if label}
    <label class="form-label" for={name}>
        {label}
    </label>
{/if}
{#if disabled}
    {#if disabledMsg}
        <Message type="warning" text={disabledMsg} />
    {:else}
        <Message type="warning" text={_('-warning-save-to-upload-')} />
    {/if}
{:else}
    <div
        class="upload"
        class:upload-image={image}
        class:upload-multiple={multiple}
        class:mt-6={inline && !label}>
        {#if assets}
            {#if multiple && image}
                {#if assets && assets.length > 0}
                    <div class="multiple-images">
                        {#each assets as asset, index}
                            <ImageValue
                                upload
                                {multiple}
                                {path}
                                image={asset.file}
                                remove={() => remove(index)}
                                {querystring}
                                {loading}
                                {size} />
                        {/each}
                    </div>
                {/if}
            {:else if !multiple && image && assets && assets.length > 0}
                <ImageValue
                    upload
                    {path}
                    {multiple}
                    image={assets[0] && assets[0].file}
                    remove={() => remove(null)}
                    {querystring}
                    {loading}
                    {size} />
            {:else if multiple && file}
                TODO
            {:else if !multiple && !image}
                <FileValue {path} asset={assets[0]} />
            {/if}
        {/if}
        {#if !assets || assets.length === 0 || multiple}
            <label
                class="dragdrop"
                class:dragging
                class:image
                for={name}
                on:drop|preventDefault={onFile(getFilesFromDrop)}
                on:dragover|preventDefault={startDragging}
                on:dragleave|preventDefault={stopDragging}>
                <div>
                    <span class="inline-block w-6 h-6 mt-4"><IcoUpload /></span>
                    {_('Neue Dateien per Drag and Drop hier einfügen oder')}
                    <u>{_('auswählen')}</u>
                </div>
                <input
                    type="file"
                    id={name}
                    {multiple}
                    on:input={onFile(getFilesFromInput)} />
            </label>
        {/if}
    </div>
{/if}

<style lang="postcss">
    .upload {
        @apply flex flex-col w-full;
        @apply md:flex-row;

        &.upload-multiple {
            @apply flex-col;
        }
    }

    .upload input {
        position: absolute !important;
        height: 1px;
        width: 1px;
        overflow: hidden;
        clip: rect(1px 1px 1px 1px);
        clip: rect(1px, 1px, 1px, 1px);
        white-space: nowrap;
    }

    .multiple-images {
        @apply flex flex-row flex-wrap justify-start gap-4 py-4;
    }

    .form-label {
        @apply mb-1;
    }

    .dragdrop {
        @apply flex flex-1 justify-center items-center;
        @apply bg-gray-100 py-4 px-2;
        @apply border-2 border-dashed border-gray-300 rounded-md;
        @apply text-center align-middle;
        @apply md:mt-0 md:h-auto;
    }
    .dragdrop:hover {
        cursor: pointer;
    }
    .dragdrop > div {
        @apply text-gray-600 text-center md:-mt-2.5;
    }
    :global(.dragdrop > div svg) {
        @apply mb-2 inline;
    }
    :global(.dragdrop > div u) {
        @apply text-sky-700;
    }
    :global(.upload-image .preview) {
        @apply md:w-2/5;
    }
</style>
