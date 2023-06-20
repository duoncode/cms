<script>
    import Logo from './Logo.svelte';
</script>

<style lang="postcss">
    .logo {
        display: flex;
        align-items: center;
        margin-top: 19px;

        :global(svg) {
            height: var(--s-6);
            width: var(--s-6);
            margin-left: -10px;
        }

        span {
            display: inline-block;
            margin-top: -1px;
            margin-left: var(--s-3);
            font-size: var(--s-5);
            font-weight: 300;
            letter-spacing: var(--s-3);
        }
    }
</style>

<div class="logo">
    <!--<Logo width />
    <span>CONIA</span> -->
    <img
        style="width: 80%; display: inline-block; margin: 0 auto;"
        src="/images/logo.png" />
</div>
