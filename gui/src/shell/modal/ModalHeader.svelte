<script lang="ts">
    import type { Snippet } from 'svelte';

    let {
        close,
        children,
        class: classes = '',
    }: {
        children: Snippet;
        class?: string;
        close?: () => void;
    } = $props();
</script>

<div class="modal-header flex items-center justify-between rounded-t border-b p-4 md:p-8 {classes}">
    <h3 class="modal-header-title text-xl font-semibold">
        {@render children()}
    </h3>
    {#if close}
        <button
            type="button"
            class="modal-close-button ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm"
            data-modal-hide="default-modal"
            onclick={close}>
            <svg
                class="h-3 w-3"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 14 14">
                <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close modal</span>
        </button>
    {/if}
</div>

<style lang="postcss">
    .modal-close-button {
        color: var(--muted-text-color);

        &:hover {
            brightnetss: filter(90%);
        }
    }
</style>
