// import '../../lib/unload';
import './styles/main.css';
import Gui from './Gui.svelte';

import system from './lib/sys';
import { loadUser } from './lib/user';

async function startApp() {
    await system.loadSettings();
    await loadUser();

    const gui = new Gui({
        target: document.getElementById('panel'),
    });

    return gui;
}

startApp();
