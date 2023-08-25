<script lang="ts">
    import Field from '$shell/Field.svelte';
    import Label from '$shell/Label.svelte';

    export let value: string;
    export let label: string;
    export let id: string;
    export let required = false;
</script>

<Field {required}>
    <Label of={id}>
        {label}
    </Label>
    <div class="mt-2">
        <input
            {id}
            name={id}
            type="password"
            {required}
            autocomplete="new-password"
            bind:value />
    </div>
</Field>
