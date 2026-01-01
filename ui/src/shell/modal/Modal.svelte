<script lang="ts">
	import type { Component, Snippet } from 'svelte';
	import type { ModalOptions } from '.';

	import IcoTimes from '$shell/icons/IcoTimes.svelte';

	import { setContext } from 'svelte';

	let { children }: { children: Snippet } = $props();
	let Content: null | Component = $state(null);
	let componentProps: object = $state({});
	let css: string = $state('');
	let options = $state<ModalOptions>({});

	function open(content: Component, attributes: object = {}, opts: ModalOptions = {}) {
		Content = content;
		componentProps = attributes;
		options = opts;
	}

	function close() {
		Content = null;
	}

	setContext('modal', { open, close });
</script>

{#if Content}
	<div
		class="modal fixed top-0 right-0 bottom-0 left-0 z-50 flex items-center justify-center bg-black/20">
		<div
			class="modal-container relative w-full max-w-3xl rounded-md"
			style={css}>
			{#if !options.hideClose}
				<button
					onclick={close}
					aria-label="close">
					<span
						class="absolute -top-2 -right-2 h-6 w-6 cursor-pointer rounded-full bg-red-600 text-white">
						<IcoTimes />
					</span>
				</button>
			{/if}
			<Content {...componentProps} />
		</div>
	</div>
{/if}
{@render children()}

<style lang="postcss">
	.modal-container {
		background-color: var(--mogal-bg-color, #fff);
	}
</style>
