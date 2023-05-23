// import '../../lib/unload';
import './styles/main.css';
import Gui from './Gui.svelte'

import { loadSettings } from './lib/boot';
import { loadUser } from './lib/user';
import { initGettext } from './lib/locale'


async function startApp() {
    await loadSettings();
    await loadUser();
    await initGettext();

    const gui = new Gui({
        target: document.getElementById('panel')
    })

    return gui;
}

startApp();
