<script>
    import IcoOctagonTimes from '$shell/icons/IcoOctagonTimes.svelte';
    import IcoShieldCheck from '$shell/icons/IcoShieldCheck.svelte';
    import IcoCircleInfo from '$shell/icons/IcoCircleInfo.svelte';
    import IcoTriangleExclamation from '$shell/icons/IcoTriangleExclamation.svelte';

    export let type;
    export let text = '';
    export let narrow = false;

    function getColor() {
        switch (type) {
            case 'success':
                return 'bg-emerald-50 border-emerald-400';
            case 'info':
                return 'bg-sky-50 border-sky-400';
            case 'hint':
            case 'warning':
                return 'bg-yellow-50 border-yellow-400';
            case 'error':
                return 'bg-rose-50 border-rose-400';
        }
    }

    function getTextColor() {
        switch (type) {
            case 'success':
                return 'text-emerald-700';
            case 'info':
                return 'text-sky-700';
            case 'hint':
            case 'warning':
                return 'text-yellow-700';
            case 'error':
                return 'text-rose-700';
        }
    }
</script>

<style type="postcss">
    :global(.message em) {
        @apply font-medium italic whitespace-nowrap;
    }
</style>

{#if type}
    <div
        class="message border-l-4 {getColor()}"
        class:py-1={narrow}
        class:px-2={narrow}
        class:p-4={!narrow}>
        <div class="flex items-top">
            <div
                class="flex-shrink-0 {getTextColor()}"
                style="margin-top: -0.15rem">
                {#if type == 'success'}
                    <IcoShieldCheck />
                {:else if type == 'info'}
                    <IcoCircleInfo />
                {:else if type == 'warning'}
                    <IcoTriangleExclamation />
                {:else if type == 'error'}
                    <IcoOctagonTimes />
                {:else}
                    <IcoCircleInfo />
                {/if}
            </div>
            <div class:ml-2={narrow} class:ml-3={!narrow}>
                <div class="text-sm {getTextColor()}">
                    {#if text}
                        {@html text}
                    {:else}
                        <slot />
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/if}
