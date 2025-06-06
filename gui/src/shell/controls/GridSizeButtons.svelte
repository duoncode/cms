<script lang="ts">
	import type { GridItem } from '$types/data';
	import type { GridField } from '$types/fields';

	import { setDirty } from '$lib/state';
	import GridButtonLabel from '$shell/controls/GridButtonLabel.svelte';
	import IcoExpand from '$shell/icons/IcoExpand.svelte';
	import IcoCollapse from '$shell/icons/IcoCollapse.svelte';
	import IcoIndent from '$shell/icons/IcoIndent.svelte';
	import IcoUnindent from '$shell/icons/IcoUnindent.svelte';

	type Props = {
		item: GridItem;
		field: GridField;
		dropdown?: boolean;
	};

	let { item = $bindable(), field = $bindable(), dropdown = false }: Props = $props();
	let widest = $derived(item.colspan === field.columns);
	let narrowest = $derived(item.colspan === field.minCellWidth);
	let highest = $derived(item.rowspan === field.columns * 2);
	let onerow = $derived(item.rowspan === 1);
	let unindented = $derived(item.colstart === null);
	let fullyindented = $derived(
		item.colstart !== null &&
			item.colstart !== undefined &&
			item.colstart + item.colspan - 1 === field.columns,
	);

	function width(val: number) {
		return () => {
			item.colspan = item.colspan + val;
			setDirty();
		};
	}

	function height(val: number) {
		return () => {
			item.rowspan = item.rowspan + val;
			setDirty();
		};
	}

	function indent(val: number) {
		return () => {
			let colstart = item.colstart;

			if (val > 0 && colstart === null) {
				item.colstart = 2;
				setDirty();
				return;
			}

			if (colstart !== null && colstart !== undefined) {
				colstart += val;
			}

			if (colstart === 0) {
				colstart = null;
			}

			item.colstart = colstart;
			setDirty();
		};
	}
</script>

<div
	class="flex flex-grow flex-row items-center gap-x-3 py-2"
	class:justify-start={!dropdown}
	class:justify-center={dropdown}>
	<button
		class="width-plus"
		disabled={widest}
		onclick={width(1)}>
		<span class="icon">
			<IcoExpand />
		</span>
		<GridButtonLabel value={item.colspan} />
	</button>
	<button
		class="width-minus"
		disabled={narrowest}
		onclick={width(-1)}>
		<span class="icon">
			<IcoCollapse />
		</span>
		<GridButtonLabel value={item.colspan} />
	</button>
	<button
		class="indent"
		disabled={fullyindented}
		onclick={indent(1)}>
		<IcoIndent />
		<GridButtonLabel value={item.colstart} />
	</button>
	<button
		class="unindent"
		disabled={unindented}
		onclick={indent(-1)}>
		<IcoUnindent />
		<GridButtonLabel value={item.colstart} />
	</button>
	<button
		class="height-plus"
		disabled={highest}
		onclick={height(1)}>
		<IcoExpand />
		<GridButtonLabel value={item.rowspan} />
	</button>
	<button
		class="height-minus"
		disabled={onerow}
		onclick={height(-1)}>
		<IcoCollapse />
		<GridButtonLabel value={item.rowspan} />
	</button>
</div>

<style lang="postcss">
	div button {
		@apply relative h-4 w-4;

		&[disabled] {
			@apply text-gray-300;
		}
	}

	.width-minus,
	.width-plus {
		span.icon {
			display: block;
			transform: rotate(90deg);
		}
	}
</style>
