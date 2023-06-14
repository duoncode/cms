<script>
    import Searchbar from '$shell/Searchbar.svelte';

    export let data;
</script>

<Searchbar />
<h1>Pages {data.collection}</h1>

<div class="mt-4">
    {#each data.pages as page}
        <div class="border-bottom">{page.title}, {page.type}</div>
    {/each}
</div>
