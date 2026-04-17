<script lang="ts">
	import type { NavItem } from '$lib/collections';
	import Link from '$shell/Link.svelte';
	import Self from './NavTree.svelte';

	type Props = {
		items: NavItem[];
		depth?: number;
	};

	let { items, depth = 0 }: Props = $props();

	const sectionStyle = (level: number) => `padding-left: calc(${level} * var(--cms-space-4))`;
	const linkStyle = (level: number) =>
		`padding-left: calc(var(--cms-space-4) + ${level} * var(--cms-space-4))`;
	const key = (item: NavItem, index: number) =>
		item.type === 'collection'
			? `collection:${item.slug}`
			: `section:${depth}:${index}:${item.name}`;
</script>

{#each items as item, index (key(item, index))}
	{#if item.type === 'section'}
		<h2
			class="cms-nav-section-title"
			style={sectionStyle(depth)}>
			{item.name}
			{#if item.meta.badge}
				<span class="cms-nav-badge">{item.meta.badge}</span>
			{/if}
		</h2>
		<Self
			items={item.children}
			depth={depth + 1} />
	{:else}
		<div
			class="cms-nav-link-row"
			style={linkStyle(depth)}>
			<Link href="collection/{item.slug}">
				{item.name}
				{#if item.meta.badge}
					<span class="cms-nav-badge">{item.meta.badge}</span>
				{/if}
			</Link>
		</div>
	{/if}
{/each}

<style lang="postcss">
	.cms-nav-badge {
		display: inline-flex;
		align-items: center;
		margin-left: var(--cms-space-2);
		padding: 0 var(--cms-space-2);
		border: var(--cms-border);
		border-radius: 999px;
		font-size: 0.75em;
		font-weight: 500;
	}

	.cms-nav-section-title {
		margin-top: var(--cms-space-6);
		font-size: var(--cms-font-size-sm);
		font-weight: 600;
	}

	.cms-nav-link-row {
		margin-top: var(--cms-space-1);
	}
</style>
