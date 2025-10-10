<script lang="ts">
	import { toggleNav } from '../lib/ui';
	import IcoBarsCenterLeft from '$shell/icons/IcoBarsCenterLeft.svelte';
</script>

<button
	class="nav h-9 w-9 bg-white p-2"
	onclick={toggleNav}>
	<IcoBarsCenterLeft />
</button>

<style lang="postcss">
	button.nav {
		position: absolute;
		left: var(--s-2);
		border: 0;
		box-shadow: none;
		cursor: pointer;
	}
</style>
