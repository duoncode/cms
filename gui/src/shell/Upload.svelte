<script lang="ts">
    import { getContext, createEventDispatcher } from 'svelte';

    import IcoUpload from '$shell/icons/IcoUpload.svelte';
    import { _ } from '$lib/locale';
    import { setDirty, error } from '$lib/state';
    import toast from '$lib/toast';
    import Image from '$shell/Image.svelte';
    import File from '$shell/File.svelte';
    import Dialog from '$shell/Dialog.svelte';
    import Message from '$shell/Message.svelte';
    import req from '$lib/req.js';

    export let url: string;
    export let image: boolean; // if present thumbs will be rendered
    export let name: string;
    export let asset = null;
    export let label = null;
    export let altempty = null;
    export let multiple = false;
    export let size = 'xl';
    export let useThumb = true;
    export let querystring = '';
    export let disabled = false;
    export let disabledMsg = null;
    export let callback = null;
    export let inline = false;

    let loading = false;
    let dragging = false;

    const dispatch = createEventDispatcher();
    const { open, close } = getContext('simple-modal');

    function remove(item) {
        if (item === null) {
            asset = null;
        } else {
            console.log('TODO delete from asset array');
        }
        setDirty();
        dispatch('dirty');
    }

    function getFilesFromDrop({ dataTransfer: { files, items } }) {
        let result = files.length
            ? [...files]
            : items
                  .filter(({ kind }) => kind === 'file')
                  .map(({ getAsFile }) => getAsFile());

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

    function getFilesFromInput({ target }) {
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

    async function upload(file) {
        let formData = new FormData();

        formData.append('file', file);
        try {
            return await req.post(url, formData);
        } catch (e) {
            if (e instanceof req.FetchError) {
                error(e.data);
            } else {
                throw e;
            }
        }
    }

    function getFileName(item) {
        if (!item) {
            return;
        }
        if (item.ok) {
            return item.renamed || item.file;
        }

        toast.add({ kind: 'error', message: item.error });

        return null;
    }

    function onFile(getFilesFunction) {
        return async event => {
            stopDragging();
            let files = getFilesFunction(event);

            if (files.length > 0) {
                loading = true;

                let responses = await Promise.all(
                    files.map(file => {
                        return upload(file).then(resp =>
                            resp ? resp.json() : null,
                        );
                    }),
                );

                if (responses.length > 0) {
                    if (multiple) {
                        asset = responses.map(item => getFileName(item));
                    } else {
                        asset = getFileName(responses[0]);
                    }
                    if (asset && callback) {
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

<style lang="postcss">
    .upload {
        @apply flex flex-col w-full;
        @apply md:flex-row;
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

    .form-label {
        @apply mb-1;
    }

    .dragdrop {
        @apply flex flex-1 justify-center items-center;
        @apply bg-gray-100 py-4 px-2 mt-2;
        @apply border-2 border-dashed border-gray-300 rounded-md;
        @apply text-center align-middle;
        @apply md:mt-0 md:h-auto;

        &.asset,
        &.image {
            @apply md:ml-2;
        }
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
        @apply text-blue-600;
    }
    :global(.upload-image .preview) {
        @apply md:w-2/5;
    }
</style>

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
        {#if multiple}
            <p><b>TODO</b>: Multiple uploads</p>
        {:else if image}
            <Image
                upload
                base={url}
                image={asset}
                remove={() => remove(null)}
                {querystring}
                {useThumb}
                {altempty}
                {loading}
                {size} />
        {:else}
            <File base={url} {asset} />
        {/if}
        <label
            class="dragdrop"
            class:dragging
            class:multiple
            class:asset
            class:image
            for={name}
            on:drop|preventDefault={onFile(getFilesFromDrop)}
            on:dragover|preventDefault={startDragging}
            on:dragleave|preventDefault={stopDragging}>
            <div>
                <IcoUpload /><br />
                {_('Drag and Drop Dateien')}
                {@html _('or <u>browse</u>.')}
            </div>
            <input
                type="file"
                id={name}
                {multiple}
                on:input={onFile(getFilesFromInput)} />
        </label>
    </div>
{/if}
