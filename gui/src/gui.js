// import '../../lib/unload';
import './styles/main.css';
import Gui from './Gui.svelte'

// import { fetchPermissions } from '../../lib/stores/permissions';
// import { loadPlugins } from '../../lib/plugins';
// import { initGettext } from './locale';


async function startApp() {
    // await initGettext();
    // await fetchPermissions();
    // await loadPlugins();

    const gui = new Gui({
        target: document.getElementById('panel')
    })

    return gui;
}

startApp();
