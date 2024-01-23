<script lang="ts">
    export let required = false;
    export { cssclass as class };

    let cssclass = null;
</script>

<div
    class="field {cssclass}"
    class:required>
    <slot />
</div>
