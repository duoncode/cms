<script lang="ts">
	import type { Snippet } from 'svelte';

	import IcoOctagonTimes from '$shell/icons/IcoOctagonTimes.svelte';
	import IcoShieldCheck from '$shell/icons/IcoShieldCheck.svelte';
	import IcoCircleInfo from '$shell/icons/IcoCircleInfo.svelte';
	import IcoTriangleExclamation from '$shell/icons/IcoTriangleExclamation.svelte';

	type Props = {
		type: any;
		text?: string;
		narrow?: boolean;
		children: Snippet;
	};

	let { type, text = '', narrow = false, children }: Props = $props();

	function getColor() {
		switch (type) {
			case 'success':
				return 'bg-emerald-50 border-emerald-400';
			case 'info':
				return 'bg-sky-50 border-sky-400';
			case 'hint':
			case 'warning':
				return 'bg-yellow-50 border-yellow-400';
			case 'error':
				return 'bg-rose-50 border-rose-400';
		}
	}

	function getTextColor() {
		switch (type) {
			case 'success':
				return 'text-emerald-700';
			case 'info':
				return 'text-sky-700';
			case 'hint':
			case 'warning':
				return 'text-yellow-700';
			case 'error':
				return 'text-rose-700';
		}
	}
</script>

{#if type}
	<div
		class="message border-l-4 {getColor()}"
		class:py-1={narrow}
		class:px-2={narrow}
		class:p-4={!narrow}>
		<div class="items-top flex">
			<div
				class="flex-shrink-0 {getTextColor()}"
				style="margin-top: -0.15rem">
				{#if type == 'success'}
					<IcoShieldCheck />
				{:else if type == 'info'}
					<IcoCircleInfo />
				{:else if type == 'warning'}
					<IcoTriangleExclamation />
				{:else if type == 'error'}
					<IcoOctagonTimes />
				{:else}
					<IcoCircleInfo />
				{/if}
			</div>
			<div
				class:ml-2={narrow}
				class:ml-3={!narrow}>
				<div class="text-sm {getTextColor()}">
					{#if text}
						{@html text}
					{:else}
						{@render children()}
					{/if}
				</div>
			</div>
		</div>
	</div>
{/if}

<style type="postcss">
	:global(.message em) {
		@apply whitespace-nowrap font-semibold italic;
	}
</style>
