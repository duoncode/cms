<script lang="ts">
	import type { Node } from '$types/data';

	import { _ } from '$lib/locale';
	import { system } from '$lib/sys';
	import { generatePaths } from '$lib/urlpaths';
	import Document from '$shell/Document.svelte';
	import Pane from '$shell/Pane.svelte';
	import Tabs from '$shell/Tabs.svelte';
	import Content from '$shell/Content.svelte';
	import Settings from '$shell/Settings.svelte';

	type Props = {
		node: Node;
		save: (published: boolean) => Promise<void>;
	};

	let { node = $bindable() }: Props = $props();

	let activeTab = $state('content');

	function changeTab(tab: string) {
		return () => {
			activeTab = tab;
		};
	}

	$effect(() => {
		if (node.route) {
			node.generatedPaths = generatePaths(node, node.route, $system);
		}
	});
</script>

<div class="flex h-screen flex-col">
	<Document>
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
