<script lang="ts">
	import type { Blueprint } from '$types/data';
	import { ModalHeader, ModalBody, ModalFooter } from '$shell/modal';
	import { goto } from '$app/navigation';
	import { _ } from '$lib/locale';
	import { base } from '$lib/req';
	import Button from '$shell/Button.svelte';

	type Props = {
		close: () => void;
		collectionSlug: string;
		blueprints: Blueprint[];
	};

	let { close, collectionSlug, blueprints }: Props = $props();

	function createNode(slug: string) {
		return () => {
			goto(`${base}collection/${collectionSlug}/create/${slug}`);
			close();
		};
	}
</script>

<ModalHeader>{_('Inhaltstyp hinzufügen')}</ModalHeader>
<ModalBody>
	<div class="mb-8 grid grid-cols-2 gap-4">
		{#if blueprints.length > 0}
			{#each blueprints as blueprint}
				<Button
					class="secondary"
					onclick={createNode(blueprint.slug)}>
					<span class="ml-2">
						{blueprint.name}
					</span>
				</Button>
			{/each}
		{/if}
	</div>
</ModalBody>
<ModalFooter>
	<div class="controls">
		<Button
			class="danger"
			onclick={close}>
			{_('Abbrechen')}
		</Button>
	</div>
</ModalFooter>
