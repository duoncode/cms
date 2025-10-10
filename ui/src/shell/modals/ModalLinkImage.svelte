<script lang="ts">
	import { system } from '$lib/sys';
	import { _ } from '$lib/locale';

	type Props = {
		node: string;
		file: string;
		current: string;
		clickFile: (path: string) => void;
	};

	let { node, file, current = $bindable(), clickFile }: Props = $props();

	const path = `${$system.prefix}/media/image/node/${node}/${file}`;
	const onclick = () => clickFile(path);
</script>

<button
	class="link-image flex items-center justify-center rounded border border-gray-400"
	{onclick}
	class:active={current && current.endsWith(`/${file}`)}>
	<img
		src="{path}?resize=fit&w=200&h=200"
		alt={_('Vorschau')}
		style="max-height: 90vh" />
</button>

<style lang="postcss">
	.link-image {
		width: 190px;
		height: 190px;
		padding: 5px;

		&.active {
			border-color: var(--color-emerald-600);
			background-color: var(--color-emerald-100);
		}
	}
</style>
