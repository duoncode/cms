<script lang="ts">
	import { _ } from '$lib/locale';
	import { ModalHeader, ModalBody, ModalFooter } from '$shell/modal';
	import Button from '$shell/Button.svelte';

	type Props = {
		add: (index: number, before: boolean, type: string) => void;
		close: () => void;
		index: number | null;
		types: { id: string; label: string }[];
	};

	let { add, close, index, types }: Props = $props();

	let type: string | null = $state(null);
	let disabled = $derived(type === null);

	function addContent(before: boolean) {
		return () => {
			if (!disabled) {
				add(index, before, type);
				close();
			}
		};
	}

	function setType(t: string) {
		return () => (type = t);
	}
</script>

<ModalHeader>
	{_('Inhaltstyp hinzufügen')}
</ModalHeader>
<ModalBody>
	<div class="mb-8 grid grid-cols-2 gap-4">
		{#if types.length > 0}
			{#each types as t}
				<Button
					class="ring-1 ring-sky-800 {t.id === type
						? 'bg-sky-800 text-white'
						: 'bg-white text-sky-800'}"
					onclick={setType(t.id)}>
					<span class="ml-2">
						{t.label}
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
		<Button
			class="primary"
			onclick={addContent(true)}
			{disabled}>
			{index === null ? _('Einfügen') : _('Davor einfügen')}
		</Button>
		{#if index !== null}
			<Button
				class="primary"
				onclick={addContent(false)}
				{disabled}>
				{_('Danach einfügen')}
			</Button>
		{/if}
	</div>
</ModalFooter>
