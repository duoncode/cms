<script lang="ts">
    import type { Node } from '$types/data';
    import Searchbar from '$shell/Searchbar.svelte';
    import Link from '$shell/Link.svelte';

    export let data;

    let searchTerm = '';

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

    function search(searchTerm: string) {
        return (node: Node) => {
            if (searchTerm.length > 0) {
                return node.columns[0].value
                    .toString()
                    .toLowerCase()
                    .includes(searchTerm.toLowerCase());
            }

            return true;
        };
    }

    $: nodes = data.nodes.filter(search(searchTerm));
</script>

<style lang="postcss">
    th {
        @apply sticky top-0 z-10 border-b border-gray-300 bg-gray-100;
        @apply bg-opacity-75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold;
        @apply text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8 border-t border-black border-opacity-10;
    }

    td {
        @apply whitespace-nowrap border-b border-gray-200 py-4 pl-4 pr-3;
        @apply text-sm text-gray-900 sm:pl-6 lg:pl-8;
    }

    tr:hover {
        td,
        td a {
            @apply bg-emerald-100;
        }
    }
</style>

<div class="flex flex-col h-full">
    <Searchbar bind:searchTerm />
    <h1 class="py-4 px-8 text-xl font-medium">
        {data.name}
    </h1>
    <div class="flex-1 px-4 sm:px-6 lg:px-8 overflow-y-auto border-gray-200">
        <div class="flow-root">
            <div class="mx-8 mb-8">
                <div
                    class="-mx-4 sm:-mx-6 lg:-mx-8 ring-1 ring-opacity-5 ring-black">
                    <div class="inline-block min-w-full align-middle shadow">
                        <table
                            class="min-w-full border-separate border-spacing-0 bg-white">
                            <thead>
                                <tr>
                                    {#each data.header as column}
                                        <th scope="col">{column}</th>
                                    {/each}
                                </tr>
                            </thead>
                            <tbody>
                                {#each nodes as node}
                                    <tr>
                                        {#each node.columns as column}
                                            <td
                                                class:font-medium={column.bold}
                                                class:font-italic={column.italic}>
                                                <Link
                                                    href="collection/{data.slug}/{node.uid}">
                                                    {#if column.date}
                                                        {fmtDate(
                                                            column.value.toString(),
                                                        )}
                                                    {:else}
                                                        {@html column.value}
                                                    {/if}
                                                </Link>
                                            </td>
                                        {/each}
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
