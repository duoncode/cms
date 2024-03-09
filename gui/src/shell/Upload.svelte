<script lang="ts">
    import type { Modal } from 'svelte-simple-modal';
    import type { FileItem, UploadResponse, UploadType } from '$types/data';
    import type { Toast } from '$lib/toast';

    import { getContext, createEventDispatcher } from 'svelte';
    import { _ } from '$lib/locale';
    import { system } from '$lib/sys';
    import { setDirty } from '$lib/state';
    import toast from '$lib/toast';
    import req from '$lib/req.js';
    import IcoUpload from '$shell/icons/IcoUpload.svelte';
    import Dialog from '$shell/Dialog.svelte';
    import Message from '$shell/Message.svelte';
    import MediaList from '$shell/MediaList.svelte';

    export let path: string;
    export let type: UploadType;
    export let name: string;
    export let translate: boolean;
    export let assets: FileItem[];
    export let multiple = false;
    export let required = false;
    export let disabled = false;
    export let disabledMsg = null;
    export let callback = null;
    export let inline = false;

    let loading = false;
    let dragging = false;
    let allowedExtensions = '';

    const dispatch = createEventDispatcher();
    const { open, close }: Modal = getContext('simple-modal');

    function remove(index: number | null) {
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
                    title: _('Fehler'),
                    body: _('In diesem Feld ist nur eine einzelne Datei erlaubt.'),
                    type: 'error',
                    close,
                },
                {
                    closeButton: false,
                },
            );
            return [];
        }

        return result;
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

    function getTitleAltValue() {
        if (translate) {
            const result: Record<string, string> = {};
            $system.locales.map(locale => (result[locale.id] = ''));
            return result;
        }

        return '';
    }

    function getError(item: UploadResponse): Toast {
        return {
            kind: 'error',
            title: _('Datei:') + ' ' + item.file,
            message: item.error,
        };
    }

    function onFile(getFilesFunction: (event: DragEvent | Event) => File[]) {
        return async (event: Event) => {
            stopDragging();
            let files = getFilesFunction(event);

            if (files.length > 0) {
                loading = true;

                let responses = await Promise.all(
                    files.map(async (file: File) => {
                        return upload(file).then(resp => resp.data);
                    }),
                );

                const value = getTitleAltValue();

                if (multiple) {
                    responses.map((item: UploadResponse) => {
                        if (item.ok) {
                            assets.push({
                                alt: value,
                                title: value,
                                file: item.file,
                            });
                            assets = [...assets];
                        } else {
                            toast.add(getError(item));
                        }
                    });
                } else {
                    const item = responses[0] as UploadResponse;

                    if (item.ok) {
                        assets = [
                            {
                                alt: value,
                                title: value,
                                file: item.file,
                            },
                        ];
                    } else {
                        toast.add(getError(item));
                    }
                }

                if (assets && callback) {
                    callback();
                }
            }

            loading = false;
            setDirty();
            dispatch('dirty');
        };
    }

    $: allowedExtensions =
        type === 'image'
            ? $system.allowedFiles.image.join(', ')
            : $system.allowedFiles.file.join(', ');
</script>

{#if disabled}
    {#if disabledMsg}
        <Message
            type="warning"
            text={disabledMsg} />
    {:else}
        <Message
            type="warning"
            text={_('-warning-save-to-upload-')} />
    {/if}
{:else}
    <div
        class="upload upload-{type}"
        class:required
        class:upload-multiple={multiple}
        class:mt-6={inline}>
        <MediaList
            bind:assets
            {multiple}
            {type}
            {path}
            {remove}
            {loading}
            {translate} />
        {#if !assets || assets.length === 0 || multiple}
            <label
                class="dragdrop"
                class:dragging
                class:image={type === 'image'}
                for={name}
                on:drop|preventDefault={onFile(getFilesFromDrop)}
                on:dragover|preventDefault={startDragging}
                on:dragleave|preventDefault={stopDragging}>
                <div class="label">
                    <span class="inline-block w-6 h-6"><IcoUpload /></span>
                    {_('Neue Dateien per Drag and Drop hier einfügen oder')}
                    <u>{_('auswählen')}</u>
                </div>
                <div class="file-extensions text-xs mt-0">
                    Erlaubte Dateiendungen: {allowedExtensions}
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
        @apply flex flex-col w-full h-full;
        @apply md:flex-row;

        &.upload-multiple {
            @apply flex-col;
        }

        &.required .dragdrop {
            @apply border-l-rose-700 border-l-4;
            border-left-style: solid;
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

    .dragdrop {
        @apply flex flex-1 flex-col justify-center items-center;
        @apply bg-gray-100 py-4 px-2;
        @apply border-2 border-dashed border-gray-300 rounded-md;
        @apply text-center align-middle;
        @apply md:mt-0 md:h-auto;
    }
    .dragdrop:hover {
        cursor: pointer;
    }
    .dragdrop > div.label {
        @apply flex flex-row justify-center gap-2 items-center text-gray-600;
    }
    :global(.dragdrop > div.label svg) {
        @apply mb-2 inline;
    }
    :global(.dragdrop > div.label u) {
        @apply text-sky-700;
    }
    :global(.upload-image .preview) {
        @apply md:w-2/5;
    }
    .dragdrop > div.file-extensions {
        @apply text-gray-400 mt-1;
        font-weight: normal;
    }
</style>
