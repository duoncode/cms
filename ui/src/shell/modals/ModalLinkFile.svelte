<script lang="ts">
	import { system } from '$lib/sys';

	type Props = {
		node: string;
		file: string;
		current: string;
		clickFile: (path: string) => void;
	};

	let { node, file, current = $bindable(), clickFile }: Props = $props();

	const path = `${$system.assets}/node/${node}/${file}`;
	const onclick = () => clickFile(path);
</script>

<div class="mt-2 pr-4">
	<button
		{onclick}
		class="inline-flex w-full items-center gap-x-1.5 rounded-md border border-emerald-600 px-3 py-2 text-sm font-semibold text-emerald-600 shadow-xs focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
		class:active={current && current.endsWith(`/${file}`)}>
		{file}
	</button>
</div>

<style lang="postcss">
	@reference "tailwindcss";

	button.active {
		@apply bg-emerald-600 text-white;
	}
</style>
