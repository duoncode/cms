<script lang="ts">
	import type { BeforeNavigate } from '@sveltejs/kit';
	import type { Collection, Node } from '$types/data';
	import type { ModalFunctions } from '$shell/modal';

	import { getContext } from 'svelte';
	import { beforeNavigate, goto } from '$app/navigation';
	import { _ } from '$lib/locale';
	import { system } from '$lib/sys';
	import { dirty, setPristine } from '$lib/state';
	import { generatePaths } from '$lib/urlpaths';
	import NodeControlBar from '$shell/NodeControlBar.svelte';
	import Breadcrumbs from '$shell/Breadcrumbs.svelte';
	import Headline from '$shell/Headline.svelte';
	import Document from '$shell/Document.svelte';
	import Pane from '$shell/Pane.svelte';
	import Tabs from '$shell/Tabs.svelte';
	import Content from '$shell/Content.svelte';
	import Settings from '$shell/Settings.svelte';
	import ModalDirty from '$shell/modals/ModalDirty.svelte';

	let { open, close } = getContext<ModalFunctions>('modal');

	beforeNavigate(({ cancel, to }: BeforeNavigate) => {
		if ($dirty) {
			if (to === null) {
				cancel();
			} else {
				cancel();
				open(
					ModalDirty,
					{
						proceed: () => {
							setPristine();
							close();
							goto(to.url);
						},
						close,
					},
					{
						hideClose: true,
					},
				);
			}
		}
	});

	type Props = {
		node: Node;
		collection: Collection;
		save: (published: boolean) => Promise<void>;
	};

	let { node = $bindable(), collection, save }: Props = $props();

	let activeTab = $state('content');
	let showPreview: string | null = $state(null);

	function changeTab(tab: string) {
		return () => {
			activeTab = tab;
		};
	}

	async function preview() {
		await save(false);
		showPreview = node.paths.de;
	}

	$effect(() => {
		if (node.route) {
			node.generatedPaths = generatePaths(node, node.route, $system);
		}
	});
</script>

<div class="flex h-screen flex-col">
	<NodeControlBar
		bind:uid={node.uid}
		collectionPath="collection/{collection.slug}"
		deletable={node.deletable}
		preview={node.type.kind === 'page' ? preview : null}
		{save} />
	<Document>
		<Breadcrumbs
			slug={collection.slug}
			name={collection.name} />
		<Headline
			published={node.published}
			showPublished={node.type.kind !== 'document'}>
			{@html node.title}
		</Headline>
		<Tabs>
			<button
				onclick={changeTab('content')}
				class:active={activeTab === 'content'}
				class="tab">
				{_('Inhalt')}
			</button>
			{#if node.type.kind !== 'document'}
				<button
					onclick={changeTab('settings')}
					class:active={activeTab === 'settings'}
					class="tab">
					{_('Einstellungen')}
				</button>
			{/if}
		</Tabs>
		<Pane>
			{#if activeTab === 'content'}
				<Content
					bind:fields={node.fields}
					bind:node />
			{:else}
				<Settings bind:node />
			{/if}
		</Pane>
	</Document>
</div>
{#if showPreview}
	<div class="preview">
		<button onclick={() => (showPreview = null)}>schließen</button>
		<iframe
			src="/preview{showPreview}"
			title="Preview">
		</iframe>
	</div>
{/if}

<style lang="postcss">
	.preview {
		@apply bg-gray-800 bg-opacity-50;
		z-index: 999;
		backdrop-filter: blur(0.5rem);
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;

		button {
			@apply rounded bg-rose-600 px-4 py-1 text-white;
			position: absolute;
			top: 5px;
			right: 5px;
		}

		iframe {
			width: 90vw;
			height: 90vh;
			margin-top: 5vh;
			margin-left: 5vw;
		}
	}
</style>
