<script lang="ts">
    import { system } from '$lib/sys';

    export let lang: string;
</script>

<span class="locale-tabs">
    {#each $system.locales as locale}
        <button
            class="locale-tab"
            class:active={locale.id === lang}
            on:click={() => (lang = locale.id)}>
            {locale.id.toUpperCase()}
        </button>
    {/each}
</span>

<style lang="postcss">
    .locale-tabs {
        @apply flex-shrink;
        float: right;
    }

    .locale-tab {
        @apply text-sm;
        display: inline-block;
        box-shadow: 0;
        padding: 0 0.5rem;
        font-weight: normal;

        &.active {
            @apply bg-gray-200 text-black rounded;
        }
    }
</style>
