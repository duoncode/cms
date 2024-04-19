<script>
    import { _ } from '$lib/locale';

    export let image;
</script>

<div class="image-preview flex justify-center items-center">
    <img
        src="{image}?resize=width&w=1280"
        alt={_('Vorschau')}
        style="max-height: 90vh" />
</div>

<style lang="postcss">
    img {
        max-width: 100%;
        min-width: 40rem;
    }
</style>
