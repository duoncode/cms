<script lang="ts">
    import type { Component, Snippet } from 'svelte';
    import type { ModalOptions } from '.';
    import { setContext } from 'svelte';

    let { children }: { children: Snippet } = $props();
    let Content: null | Component = $state(null);
    let componentProps: object = $state({});
    let css: string = $state('');

    function open(content: Component, attributes: object = {}, options: ModalOptions = {}) {
        Content = content;
        componentProps = attributes;
        options;
    }

    function close() {
        Content = null;
    }

    setContext('modal', { open, close });
</script>

{#if Content}
    <div
        class="modal fixed bottom-0 left-0 right-0 top-0 z-50 flex items-center justify-center bg-black bg-opacity-20">
        <div
            class="modal-container w-full max-w-3xl rounded-md"
            style={css}>
            <Content {...componentProps} />
        </div>
    </div>
{/if}
{@render children()}

<style lang="postcss">
    .modal-container {
        background-color: var(--mogal-bg-color, #fff);
    }
</style>
