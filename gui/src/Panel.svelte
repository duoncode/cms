<script>
    import Modal from "svelte-simple-modal";
    import Router from "svelte-spa-router";

    import getRoutes from "./routes";

    export let prefix = null;
</script>

<Modal>
    <main>
        <Router routes={getRoutes(prefix).get()} />
    </main>
</Modal>
