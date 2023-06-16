<script>
    import { _ } from '$lib/locale';

    export let image;
</script>

<div class="image-preview">
    <img src={image} alt={_('Preview')} />
</div>
