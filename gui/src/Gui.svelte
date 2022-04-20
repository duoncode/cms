<script>
    import Router from 'svelte-spa-router';
    import { replace, location } from 'svelte-spa-router';
    import { wrap } from 'svelte-spa-router/wrap';

    import { authenticated, rememberedRoute } from './lib/user';
    import Panel from './Panel.svelte';
    import Login from './Login.svelte';

    function redirect() {
        if ($authenticated) {
            replace($rememberedRoute);
            $rememberedRoute = '/';
        } else {
            replace('/login');
            $rememberedRoute = $location;
        }
    }
</script>

<Router
    routes={{
        '/login': wrap({
            component: Login,
            conditions: [() => !$authenticated],
        }),
        '*': wrap({
            component: Panel,
            conditions: [() => $authenticated],
        }),
    }}
    on:conditionsFailed={redirect} />
