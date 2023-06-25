<script lang="ts">
    export let value: boolean;

    function toggle() {
        value = !value;
    }
</script>

<!-- Enabled: "bg-indigo-600", Not Enabled: "bg-gray-200" -->
<button
    type="button"
    on:click={toggle}
    class="{value
        ? 'bg-emerald-600 focus:ring-emerald-600'
        : 'bg-rose-600 focus:ring-rose-600'} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2"
    role="switch"
    aria-checked="false"
    aria-labelledby="availability-label"
    aria-describedby="availability-description">
    <span
        aria-hidden="true"
        class="{value
            ? 'translate-x-5'
            : 'translate-x-0'} pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" />
</button>
