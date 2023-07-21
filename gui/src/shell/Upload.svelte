<script lang="ts">
    import type { Modal } from 'svelte-simple-modal';
    import type { FileItem, UploadResponse } from '$types/data';
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
    export let image = false; // if present thumbs will be rendered
    export let name: string;
    export let translate: boolean;
    export let assets: FileItem[];
    export let multiple = false;
    export let disabled = false;
    export let disabledMsg = null;
    export let callback = null;
    export let inline = false;

    let loading = false;
    let dragging = false;

    const dispatch = createEventDispatcher();
    const { open, close }: Modal = getContext('simple-modal');

    function remove(index: number|null) {
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
                    body: _(
                        'In diesem Feld ist nur eine einzelne Datei erlaubt.',
                    ),
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
</script>

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
        class:mt-6={inline}>
        <MediaList bind:assets {multiple} {image} {path} {remove} {loading} {translate}/>
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
