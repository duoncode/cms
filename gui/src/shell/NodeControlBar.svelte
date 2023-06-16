<script lang="ts">
    import { _ } from '$lib/locale';
    import NavToggle from './NavToggle.svelte';

    export let uid: string;
</script>

<style lang="postcss">
</style>

<div class="headerbar">
    <NavToggle />
    <div class="controls" />
</div>
