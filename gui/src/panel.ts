// import '../../lib/unload';
import './styles/main.css';
import App from './App.svelte'

// import { fetchPermissions } from '../../lib/stores/permissions';
// import { loadPlugins } from '../../lib/plugins';
// import { initGettext } from './locale';


async function startApp() {
    // await initGettext();
    // await fetchPermissions();
    // await loadPlugins();

    const app = new App({
        target: document.getElementById('app')
    })

    return app;
}

startApp();
