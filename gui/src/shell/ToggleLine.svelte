<script lang="ts">
	import Toggle from '$shell/Toggle.svelte';

	type Props = {
		title: string;
		subtitle: string;
		value: boolean;
	};

	let { title, subtitle, value = $bindable() }: Props = $props();
</script>

<div class="flex items-center justify-between">
	<span class="flex flex-grow flex-col">
		<span
			class="text-sm font-semibold leading-6 text-gray-900"
			id="availability-label">
			{title}
		</span>
		<span
			class="text-sm text-gray-500"
			id="availability-description">
			{subtitle}
		</span>
	</span>
	<Toggle bind:value />
</div>
