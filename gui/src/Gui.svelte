<script>
    import Router from 'svelte-spa-router';
    import { replace } from 'svelte-spa-router';
    import { wrap } from 'svelte-spa-router/wrap';

    import { authenticated } from './lib/user';
    import Panel from './Panel.svelte';
    import Login from './Login.svelte';

    function redirect() {
        if ($authenticated) {
            replace('/');
        } else {
            replace('/login');
        }
    }
</script>

<Router
    routes={{
        '/': wrap({
            component: Panel,
            conditions: [() => $authenticated],
        }),
        '/login': wrap({
            component: Login,
            conditions: [() => !$authenticated],
        }),
    }}
    on:conditionsFailed={redirect} />
