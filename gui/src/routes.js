import Router from './lib/router';

import Dashboard from './pages/Dashboard.svelte';

const routes = new Router();

routes.add('/', Dashboard);
routes.add('/login', Login);

export default routes;

