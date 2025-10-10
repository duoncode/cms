<script lang="ts">
	import type { Component, Snippet } from 'svelte';
	import type { HTMLButtonAttributes } from 'svelte/elements';

	let openMenu = $state(false);

	function closeMenu() {
		openMenu = false;
	}

	type Props = {
		class?: string;
		icon?: Component;
		label: string;
		children: Snippet<[closeMenu: () => void]>;
	};

	let {
		class: cls = 'primary',
		icon = null,
		label,
		children,
		...attributes
	}: Props & HTMLButtonAttributes = $props();
</script>

<div class="inline-flex rounded-md shadow-xs">
	<button
		type="button"
		class="{cls} inline-flex items-center justify-center gap-x-2 rounded-l-md px-3.5 py-2.5 text-sm font-semibold shadow-xs focus:z-10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
		{...attributes}>
		{#if icon}
			{@const Icon = icon}
			<span class="-ml-0.5 h-5 w-5">
				<Icon />
			</span>
		{/if}
		{label}
	</button>
	<div class="relative -ml-px block">
		<button
			type="button"
			class="{cls} relative inline-flex items-center rounded-r-md border-l border-gray-300 p-2.5 text-gray-400 focus:z-10"
			id="option-menu-button"
			aria-expanded="true"
			aria-haspopup="true"
			onclick={() => (openMenu = !openMenu)}>
			<span class="sr-only">Open options</span>
			<svg
				class="h-5 w-5"
				viewBox="0 0 20 20"
				fill="currentColor"
				aria-hidden="true">
				<path
					fill-rule="evenodd"
					d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
					clip-rule="evenodd" />
			</svg>
		</button>
		{#if openMenu}
			<div
				class="button-menu {cls} absolute right-0 z-10 -mr-1 mt-2 w-64 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
				role="menu"
				aria-orientation="vertical"
				aria-labelledby="option-menu-button"
				tabindex="-1">
				<div
					class="py-1"
					role="none">
					{@render children(closeMenu)}
				</div>
			</div>
		{/if}
	</div>
</div>
