<script lang="ts">
	import IcoChevronRight from '$shell/icons/IcoChevronRight.svelte';
	import IcoDocumentTree from '$shell/icons/IcoDocumentTree.svelte';
	import Link from '$shell/Link.svelte';

	type Props = {
		slug: string;
		name: string;
	};

	let { slug, name }: Props = $props();
</script>

<div class="breadcrumbs mb-4 mt-8 flex flex-row items-center gap-3">
	<IcoDocumentTree />
	<IcoChevronRight />
	<span>
		<Link
			href="collection/{slug}"
			class="hover:underline">
			{name}
		</Link>
	</span>
</div>

<style lang="postcss">
	.breadcrumbs :global(svg) {
		color: #999;
		display: inline-block;
	}
</style>
