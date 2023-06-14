<script>
    import Searchbar from '$shell/Searchbar.svelte';

    export let data;
</script>

<Searchbar />
<h1>Pages {data.collection}</h1>
