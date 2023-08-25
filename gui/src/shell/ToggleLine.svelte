<script lang="ts">
    import Toggle from '$shell/Toggle.svelte';

    export let title: string;
    export let subtitle: string;
    export let value: boolean;
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
