<script lang="ts">
    import LocaleTabs from '$shell/LocaleTabs.svelte';

    export let translate = false;
    export let lang: string | null = null;
</script>

<div class="form-label">
    <div><slot /></div>
    {#if translate}
        <LocaleTabs bind:lang />
    {/if}
</div>
