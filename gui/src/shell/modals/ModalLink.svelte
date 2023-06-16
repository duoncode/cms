<script>
    import { _ } from '$lib/locale';

    export let close;
    export let add;

    let value;
    let target = '';

    function clickAdd() {
        if (value) {
            add(value, target);
            close();
        }
    }
</script>

<div class="message">
    <h2 class="text-xl font-bold">{_('Add link')}</h2>
    <div class="mt-4">
        {_('Please enter a valid URL')}
    </div>
    <div class="mt-4">
        <input class="form-input" type="text" bind:value />
    </div>
    <div class="mt-2">
        <div class="relative flex items-start">
            <div class="flex h-6 items-center">
                <input
                    id="modallink_target"
                    aria-describedby="comments-description"
                    name="modallink_target"
                    type="checkbox"
                    bind:value={target}
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
            </div>
            <div class="ml-3 text-sm leading-6">
                <label for="modallink_target" class="font-medium text-gray-900">
                    {_('open in new window')}
                </label>
            </div>
        </div>
    </div>
    <p class="mt-8 text-right">
        <button class="button danger" on:click={close}>
            {_('Cancel')}
        </button>
        <button class="button" on:click={clickAdd} disable={!value}>
            {_('Add link')}
        </button>
    </p>
</div>
