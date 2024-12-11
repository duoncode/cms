<script lang="ts">
	import { quintOut } from 'svelte/easing';
	import { crossfade } from 'svelte/transition';
	import { flip } from 'svelte/animate';
	import IcoTimes from '$shell/icons/IcoTimes.svelte';
	import toasts from '$lib/toast';
	import Toast from './Toast.svelte';

	type Props = {
		center?: boolean;
	};

	let { center = false }: Props = $props();

	const [send, receive] = crossfade({
		duration: d => Math.sqrt(d * 200),

		fallback(node) {
			const style = getComputedStyle(node);
			const transform = style.transform === 'none' ? '' : style.transform;

			return {
				duration: 600,
				easing: quintOut,
				css: t => `
                    transform: ${transform} scale(${t});
                    opacity: ${t}
                `,
			};
		},
	});

	function remove(toast) {
		return () => {
			toasts.remove(toast);
		};
	}
</script>

<div
	class="toasts fixed z-50 text-sm"
	class:pos-bottom={!center}
	class:pos-center={center}>
	{#each $toasts as toast (toast)}
		<button
			onclick={remove(toast)}
			class="toast relative mb-2 block px-4 pb-5 pt-4 last:mb-4"
			class:mr-4={!center}
			class:bg-emerald-600={toast.kind === 'success'}
			class:bg-rose-700={toast.kind === 'error'}
			class:bg-yellow-500={toast.kind === 'warning'}
			animate:flip={{ duration: 150 }}
			in:receive={{ key: toast }}
			out:send={{ key: toast }}>
			<Toast {toast} />
			{#if toast.kind === 'error'}
				<span class="absolute right-1 top-1 h-4 w-4 cursor-pointer rounded-full text-white">
					<IcoTimes />
				</span>
			{/if}
		</button>
	{/each}
</div>

<style lang="postcss">
	.pos-bottom {
		@apply bottom-0 right-0 pr-8;
	}
</style>
