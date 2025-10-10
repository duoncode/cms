<script lang="ts">
	import type { GridItem } from '$types/data';
	import type { ModalFunctions } from '$shell/modal';

	import { getContext } from 'svelte';
	import IcoTrash from '$shell/icons/IcoTrash.svelte';
	import IcoArrowUp from '$shell/icons/IcoArrowUp.svelte';
	import IcoArrowDown from '$shell/icons/IcoArrowDown.svelte';
	import IcoCirclePlus from '$shell/icons/IcoCirclePlus.svelte';
	import ModalRemove from '$shell/modals/ModalRemove.svelte';
	import { setDirty } from '$lib/state';

	type Props = {
		data: GridItem[];
		item: GridItem;
		index: number;
		add: () => void;
		dropdown?: boolean;
	};

	let {
		data = $bindable(),
		item = $bindable(),
		index = $bindable(),
		add,
		dropdown = false,
	}: Props = $props();
	let { open, close } = getContext<ModalFunctions>('modal');
	let first = $derived(data?.indexOf(item) === 0);
	let last = $derived(data?.indexOf(item) === data.length - 1);

	async function remove() {
		open(
			ModalRemove,
			{
				close,
				proceed: () => {
					data.splice(index, 1);
					data = data;
					setDirty();
					close();
				},
			},
			{},
		);
	}

	function up() {
		if (first) {
			return;
		}

		data.splice(index - 1, 0, data.splice(index, 1)[0]);
		data = data;
		setDirty();
	}

	function down() {
		if (last) {
			return;
		}

		data.splice(index + 1, 0, data.splice(index, 1)[0]);
		data = data;
		setDirty();
	}
</script>

<div
	class="flex flex-grow flex-row items-center gap-x-3 py-2"
	class:justify-end={!dropdown}
	class:mr-3={!dropdown}
	class:justify-center={dropdown}>
	<button
		class="remove"
		onclick={remove}>
		<IcoTrash />
	</button>
	<button
		class="up-down"
		disabled={last}
		onclick={down}>
		<IcoArrowDown />
	</button>
	<button
		class="up-down"
		disabled={first}
		onclick={up}>
		<IcoArrowUp />
	</button>
	<button
		class="add"
		onclick={add}>
		<IcoCirclePlus />
	</button>
</div>

<style lang="postcss">
	div button {
		height: var(--spacing-4);
		width: var(--spacing-4);

		&[disabled] {
			color: rgb(209 213 219);
		}
	}

	.remove {
		color: var(--color-orange-700);
	}

	.add {
		color: var(--color-sky-700);
	}
</style>
