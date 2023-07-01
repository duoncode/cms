<script>
    import { _ } from '$lib/locale';

    export let image;
</script>

<div class="image-preview flex justify-center items-center">
    <img src={image} alt={_('Preview')} style="max-height: 90vh" />
</div>
