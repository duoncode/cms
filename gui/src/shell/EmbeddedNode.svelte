<script lang="ts">
	import type { Node } from '$types/data';

	import Modal from '$shell/modal/Modal.svelte';
	import { _ } from '$lib/locale';
	import { system } from '$lib/sys';
	import { generatePaths } from '$lib/urlpaths';
	import Document from '$shell/Document.svelte';
	import Pane from '$shell/Pane.svelte';
	import Tabs from '$shell/Tabs.svelte';
	import ButtonMenu from '$shell/ButtonMenu.svelte';
	import IcoSave from '$shell/icons/IcoSave.svelte';
	import ButtonMenuEntry from '$shell/ButtonMenuEntry.svelte';
	import Content from '$shell/Content.svelte';
	import Settings from '$shell/Settings.svelte';

	type Props = {
		node: Node;
		save: (published: boolean) => Promise<void>;
	};

	let { node = $bindable(), save }: Props = $props();

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

<Modal>
	<div class="flex h-screen flex-col">
		<div class="embed-control-bar sticky border-b border-gray-300 bg-white py-4">
			<div class="mx-auto flex w-full max-w-7xl flex-row justify-end px-8">
				<ButtonMenu
					class="primary"
					icon={IcoSave}
					onclick={() => save(false)}
					label={_('Speichern')}>
					{#snippet children(closeMenu)}
						<ButtonMenuEntry
							onclick={() => {
								save(true), closeMenu();
							}}>
							{_('Speichern und veröffentlichen')}
						</ButtonMenuEntry>
					{/snippet}
				</ButtonMenu>
			</div>
		</div>
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
</Modal>
