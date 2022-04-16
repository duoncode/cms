// import '../../lib/unload';
import './styles/main.css';
import Gui from './Gui.svelte'

import { loadSettings } from './lib/settings';
import { loadUser } from './lib/user';


async function startApp() {
    await loadSettings();
    await loadUser();

    const gui = new Gui({
        target: document.getElementById('panel')
    })

    return gui;
}

startApp();
