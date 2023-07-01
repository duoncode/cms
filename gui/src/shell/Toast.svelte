<script lang="ts">
    import type { Toast } from '$lib/toast';

    export let toast: Toast;
</script>

<div class="text-white text-left">
    {#if toast.title}
        <h3>{toast.title}</h3>
    {/if}
    {#if toast.message}
        <p>{toast.message}</p>
    {/if}
</div>

<style lang="postcss">
    h3,
    h4 {
        @apply font-medium mt-3 mb-1;
    }

    h3:first-child {
        @apply mt-0;
    }

    h4 {
        @apply text-gray-200;
    }

    ul {
        @apply ml-4 mt-2 list-disc;
    }
</style>
