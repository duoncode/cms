// import '../../lib/unload';
import './styles/main.css';
import Gui from './Gui.svelte'

import { loadSettings } from './lib/settings';


async function startApp() {
    await loadSettings();

    const gui = new Gui({
        target: document.getElementById('panel')
    })

    return gui;
}

startApp();
