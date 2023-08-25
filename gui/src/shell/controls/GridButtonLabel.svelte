<script lang="ts">
    export let value: number | null;
</script>

{#if value !== null}
    <span class="grid-button-label text-white text-xs rounded-full bg-rose-800 absolute block">
        {value}
    </span>
{/if}

<style lang="postcss">
    span {
        @apply font-semibold flex items-center justify-center;
        font-size: 9px;
        height: 12px;
        width: 12px;
        line-height: 9px;
        top: -13px;
        left: 3px;
    }
</style>
