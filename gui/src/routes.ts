import Router from './lib/router';

import Dashboard from './pages/Dashboard.svelte';
import Pages from './pages/Pages.svelte';
import NotFound from './pages/NotFound.svelte';

export default function getRoutes() {
    const routes = new Router();

    routes.add('/', Dashboard);
    routes.add('/pages', Pages);
    routes.add('*', NotFound);

    return routes;
}
