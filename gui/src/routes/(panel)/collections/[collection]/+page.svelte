<script lang="ts">
    import Searchbar from '$shell/Searchbar.svelte';

    export let data;

    function fmtDate(d: string) {
        const date = new Date(d);

        return date.toLocaleDateString('de-DE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }
</script>

<style lang="postcss">
    th {
        @apply sticky top-0 z-10 border-b border-gray-300 bg-white;
        @apply bg-opacity-75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold;
        @apply text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8;
    }

    td {
        @apply whitespace-nowrap border-b border-gray-200 py-4 pl-4 pr-3;
        @apply text-sm text-gray-900 sm:pl-6 lg:pl-8;
    }

    tr {
        @apply hover:bg-white;
    }
</style>

<div class="flex flex-col h-full">
    <Searchbar />
    <h1 class="py-4 px-8 text-xl font-medium">
        {data.title}
    </h1>
    <div
        class="flex-1 px-4 sm:px-6 lg:px-8 overflow-y-auto border-t border-gray-200">
        <div class="flow-root">
            <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle">
                    <table class="min-w-full border-separate border-spacing-0">
                        <thead>
                            <tr>
                                <th scope="col">Titel</th>
                                <th scope="col">Seitentyp</th>
                                <th scope="col"> Editor </th>
                                <th scope="col">Zuletzt bearbeitet</th>
                                <th scope="col">Erstellt</th>
                                <th scope="col">
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {#each data.nodes as node}
                                <tr>
                                    <td class="font-medium">{node.title}</td>
                                    <td>{node.type}</td>
                                    <td>{node.editor}</td>
                                    <td>{fmtDate(node.changed)}</td>
                                    <td>{fmtDate(node.created)}</td>
                                    <td class="relative">
                                        <a
                                            href="/panel/collection/{data.slug}/"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            Bearbeiten
                                        </a>
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
