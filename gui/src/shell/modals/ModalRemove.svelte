<script lang="ts">
	import { _ } from '$lib/locale';
	import { ModalHeader, ModalBody, ModalFooter } from '$shell/modal';
	import Button from '$shell/Button.svelte';

	type Props = {
		close: any;
		proceed: any;
		message?: any;
	};

	let { close, proceed, message = null }: Props = $props();
</script>

<ModalHeader>{_('Dokument löschen')}</ModalHeader>
<ModalBody>
	<p>
		{_('Soll der Eintrag wirklich gelöscht werden?')}
	</p>
	{#if message}
		<p class="mt-6 font-semibold">{message}</p>
	{/if}
</ModalBody>
<ModalFooter>
	<div class="controls">
		<Button
			class="danger"
			onclick={proceed}>
			{_('Ja, Eintrag löschen')}
		</Button>
		<Button
			class="secondary"
			onclick={close}>
			{_('Nein, Eintrag nicht löschen')}
		</Button>
	</div>
</ModalFooter>
