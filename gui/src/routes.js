import Router from './lib/router';

import Dashboard from './pages/Dashboard.svelte';

export default function getRoutes(prefix) {
    const routes = new Router();

    routes.add('/', Dashboard);

    return routes;
}

