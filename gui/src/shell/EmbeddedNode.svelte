<script lang="ts">
	import type { Node } from '$types/data';

	import Modal from '$shell/modal/Modal.svelte';
	import { _ } from '$lib/locale';
	import { system } from '$lib/sys';
	import { dirty } from '$lib/state';
	import { generatePaths } from '$lib/urlpaths';
	import Document from '$shell/Document.svelte';
	import Pane from '$shell/Pane.svelte';
	import Tabs from '$shell/Tabs.svelte';
	import ButtonMenu from '$shell/ButtonMenu.svelte';
	import IcoSave from '$shell/icons/IcoSave.svelte';
	import ButtonMenuEntry from '$shell/ButtonMenuEntry.svelte';
	import Content from '$shell/Content.svelte';
	import Settings from '$shell/Settings.svelte';
	import Published from '$shell/Published.svelte';

	type Props = {
		node: Node;
		save: (published: boolean) => Promise<void>;
		fields: string[];
	};

	let { node = $bindable(), save, fields }: Props = $props();

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
			<div class="mx-auto flex w-full max-w-7xl flex-row px-8">
				<div class="embed-status-bar flex flex-grow flex-row items-center justify-start">
					<span class="inline-flex items-center">
						<Published
							published={node.published}
							large />
					</span>
					{#if $dirty}
						<span
							class="dirty-indicator ml-3 rounded-full bg-rose-600 px-2 pb-px text-sm font-bold text-white">
							!
						</span>
					{/if}
				</div>
				<ButtonMenu
					class="primary"
					icon={IcoSave}
					onclick={() => save(false)}
					label={_('Speichern')}>
					{#snippet children(closeMenu)}
						<ButtonMenuEntry
							onclick={() => {
								save(true);
								closeMenu();
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
						visibleFields={fields}
						bind:node />
				{:else}
					<Settings bind:node />
				{/if}
			</Pane>
		</Document>
	</div>
</Modal>
