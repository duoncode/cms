<script lang="ts">
	import type { ListedNode } from '$types/data';
	import Searchbar from '$shell/Searchbar.svelte';
	import Published from '$shell/Published.svelte';
	import Link from '$shell/Link.svelte';

	let { data } = $props();

	let searchTerm = $state('');
	let regex: RegExp | null = null;

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
		return (node: ListedNode) => {
			if (searchTerm.length > 1) {
				regex = new RegExp(
					`(${escapeRegExp(searchTerm)})`,
					/\p{Lu}/u.test(searchTerm) ? 'g' : 'gi',
				);
				return node.columns.some(col => {
					return regex?.test(col.value.toString() ?? '');
				});
			}

			regex = null;
			return true;
		};
	}

	function highlightSearchterm(value: string) {
		if (searchTerm.length < 2 || !regex) return value;

		return value.replace(regex, `<span class="search-hl">$1</span>`);
	}

	function escapeRegExp(string: string) {
		return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
	}

	let nodes = $derived(data.nodes.filter(search(searchTerm)));
</script>

<div class="flex h-full flex-col">
	<Searchbar
		bind:searchTerm
		collectionSlug={data.slug}
		blueprints={data.blueprints} />
	<h1 class="px-8 py-4 text-xl font-semibold">
		{data.name}
	</h1>
	<div class="flex-1 overflow-y-auto border-gray-200 px-4 sm:px-6 lg:px-8">
		<div class="flow-root">
			<div class="mx-8 mb-8">
				<div class="-mx-4 ring-1 ring-black/5 sm:-mx-6 lg:-mx-8">
					<div class="inline-block min-w-full align-middle shadow">
						<table class="min-w-full border-separate border-spacing-0 bg-white">
							<thead>
								<tr>
									{#if data.showPublished}
										<th class="published"></th>
									{/if}
									{#each data.header as column, i (i)}
										<th scope="col">{column}</th>
									{/each}
								</tr>
							</thead>
							<tbody>
								{#each nodes as node (node)}
									<tr>
										{#if data.showPublished}
											<td class="published text-center align-middle">
												<span class="inline-block pb-1">
													<Published published={node.published} />
												</span>
											</td>
										{/if}
										{#each node.columns as column (column)}
											<td
												class:font-semibold={column.bold}
												class:font-italic={column.italic}>
												<Link href="collection/{data.slug}/{node.uid}">
													{#if column.date}
														{fmtDate(column.value.toString())}
													{:else}
														{@html highlightSearchterm(column.value)}
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

<style lang="postcss">
	th,
	td {
		padding-left: var(--spacing-3);
		padding-right: var(--spacing-3);
		padding-top: var(--spacing-4);
		padding-bottom: var(--spacing-4);
	}

	@media (min-width: var(--breakpoint-sm)) {
		th,
		td {
			padding-left: var(--spacing-4);
			padding-right: var(--spacing-4);
		}
	}

	@media (min-width: var(--breakpoint-lg)) {
		th,
		td {
			padding-left: var(--spacing-6);
			padding-right: var(--spacing-6);
		}
	}

	th {
		position: sticky;
		top: 0;
		z-index: 10;
		border-top-width: 1px;
		border-bottom-width: 1px;
		border-color: var(--color-gray-300);
		background-color: color-mix(in srgb, var(--color-gray-100) 75%, transparent);
		text-align: left;
		font-size: var(--font-size-sm);
		font-weight: 600;
		color: var(--color-gray-900);
		backdrop-filter: blur(var(--blur-sm));
	}

	td {
		border-bottom: 1px solid var(--color-gray-200);
		white-space: nowrap;
		font-size: var(--font-size-sm);
		color: var(--color-gray-900);
	}

	.published {
		padding-right: 0;
	}

	:global(.search-hl) {
		background: #ffea50;
		border: 1px solid #ffb100;
		border-radius: 0.25rem;
	}

	tr:hover {
		td,
		:global(td a) {
			background-color: var(--color-emerald-100);
		}
	}
</style>
