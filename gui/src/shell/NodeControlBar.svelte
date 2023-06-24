<script lang="ts">
    import { _ } from '$lib/locale';
    import NavToggle from './NavToggle.svelte';
    import IcoTrash from '$shell/icons/IcoTrash.svelte';
    import IcoSave from '$shell/icons/IcoSave.svelte';

    export let uid: string;
</script>

<style lang="postcss">
    button {
        @apply relative border font-medium rounded-md;
        @apply py-2 px-3 text-left focus:outline-none transition;
        @apply flex flex-row items-center;

        span {
            @apply inline-block w-5 h-5 mr-2;
        }
    }

    .danger {
        @apply text-white bg-emerald-600 border-emerald-600;
    }

    .primary {
        @apply text-white bg-emerald-800 border-emerald-800;
    }
</style>

<div class="headerbar">
    <NavToggle />
    <div class="controls flex flex-row gap-4 justify-end px-4">
        <button class="danger">
            <span>
                <IcoTrash />
            </span>
            Löschen
        </button>
        <button class="primary">
            <span>
                <IcoSave />
            </span>
            Speichern
        </button>
    </div>
</div>
