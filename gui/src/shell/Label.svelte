<script lang="ts">
    import LocaleTabs from '$shell/LocaleTabs.svelte';

    export let of: string;
    export let translate = false;
    export let lang: string | null = null;
</script>

<label for={of} class="flex flex-row">
    <div class="flex-grow"><slot /></div>
    {#if translate}
        <LocaleTabs bind:lang />
    {/if}
</label>
