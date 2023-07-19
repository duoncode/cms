<script lang="ts">
    import type { GridYoutube } from '$types/data';
    import type { GridField } from '$types/fields';

    import { _ } from '$lib/locale';
    import Setting from '$shell/Setting.svelte';

    export let field: GridField;
    export let item: GridYoutube;
    export let index: number;

    let showSettings = false;
    let percent = 56.25; // defaults to 16:9

    if (!item.value) {
        showSettings = true;
    }

    $: {
        let x = item.aspectRatioX ? item.aspectRatioX : 16;
        let y = item.aspectRatioY ? item.aspectRatioY : 9;

        percent = parseFloat(((y / x) * 100).toFixed(2));
    }
</script>

<slot edit={() => showSettings = !showSettings} />

{#if showSettings}
    <Setting>
        <label for={field.name + '_' + index + '_ytid'}>
            {_('Youtube-ID')}
        </label>
        <div class="mt-2">
            <input
                id={field.name + '_' + index + '_ytid'}
                name={field.name + '_' + index + '_ytid'}
                type="text"
                maxlength="20"
                placeholder={_('Fügen Sie hier die Youtube-ID ein')}
                bind:value={item.value} />
        </div>
    </Setting>
    <Setting>
        <label for={field.name + '_' + index + '_x'}>
            {_('Seitenverhältnis')}
        </label>
        <div class="mt-2 flex flex-row gap-4">
            <input
                id={field.name + '_' + index + '_x'}
                name={field.name + '_' + index + '_x'}
                type="number"
                max="100"
                min="1"
                placeholder={_('Breite')}
                bind:value={item.aspectRatioX} />
            <input
                id={field.name + '_' + index + '_y'}
                name={field.name + '_' + index + '_y'}
                type="number"
                max="100"
                min="1"
                placeholder={_('Höhe')}
                bind:value={item.aspectRatioY} />
        </div>
    </Setting>
{:else}
    <div class="youtube-container">
        <div class="relative" style="padding-top: {percent}%">
            <iframe
                class="youtube absolute top-0 left-0 w-full h-full"
                title="Youtube Video"
                src="https://www.youtube.com/embed/{item.value}"
                allowfullscreen />
        </div>
    </div>
{/if}
