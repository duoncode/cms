<script lang="ts">
    import { _ } from '$lib/locale';
    import NavToggle from './NavToggle.svelte';

    export let uid: string;
</script>

<style lang="postcss">
    button {
        @apply relative border font-medium rounded-md;
        @apply p-2 text-left focus:outline-none transition;
    }

    .danger {
        @apply text-white bg-orange-700 border-orange-700;
    }

    .primary {
        @apply text-white bg-sky-700 border-sky-700;
    }
</style>

<div class="headerbar">
    <NavToggle />
    <div class="controls flex flex-row gap-4 justify-end px-4">
        <button class="danger"> Löschen </button>
        <button class="primary"> Speichern </button>
    </div>
</div>
