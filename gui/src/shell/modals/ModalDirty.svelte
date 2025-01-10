<script lang="ts">
	import { _ } from '$lib/locale';
	import { ModalHeader, ModalBody, ModalFooter } from '$shell/modal';
	import Button from '$shell/Button.svelte';

	type Props = {
		close: any;
		proceed: any;
	};

	let { close, proceed }: Props = $props();
</script>

<ModalHeader>{_('Bestätigung erforderlich')}</ModalHeader>
<ModalBody>
	<p>
		{_(
			'Die Daten in diesem Dokument wurden geändert. Die Änderungen gehen verloren, wenn Sie sie nicht speichern.',
		)}
	</p>
	<p>
		<b>
			{_('Wollen Sie die Änderungen wirklich verwerfen?')}
		</b>
	</p>
</ModalBody>
<ModalFooter>
	<div class="controls">
		<Button
			class="danger"
			onclick={proceed}>
			{_('Ja, Änderungen verwerfen')}
		</Button>
		<Button
			class="secondary"
			onclick={close}>
			{_('Nein, Dokument weiter bearbeiten')}
		</Button>
	</div>
</ModalFooter>
