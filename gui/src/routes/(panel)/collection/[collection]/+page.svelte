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

    tr:hover td {
        @apply bg-white text-red-600;
    }

    svg {
        display: inline-block;
        margin-right: 0.5rem;
        color: red;
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
                                    <td class="font-medium">
                                        <a
                                            href="/panel/collection/{data.slug}/{node.uid}">
                                            {node.title}
                                        </a>
                                    </td>
                                    <td>{node.type}</td>
                                    <td>{node.editor}</td>
                                    <td>{fmtDate(node.changed)}</td>
                                    <td>{fmtDate(node.created)}</td>
                                    <td class="relative">
                                        <a
                                            href="/panel/collection/{data.slug}/{node.uid}"
                                            class="text-red-600 hover:text-red-900 flex flex-row items-center">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                height="1em"
                                                viewBox="0 0 512 512"
                                                ><!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path
                                                    d="M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152V424c0 48.6 39.4 88 88 88H360c48.6 0 88-39.4 88-88V312c0-13.3-10.7-24-24-24s-24 10.7-24 24V424c0 22.1-17.9 40-40 40H88c-22.1 0-40-17.9-40-40V152c0-22.1 17.9-40 40-40H200c13.3 0 24-10.7 24-24s-10.7-24-24-24H88z" /></svg>
                                            bearbeiten
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
