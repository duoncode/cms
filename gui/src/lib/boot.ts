import { writable, get } from 'svelte/store';
import req from './req';

type Settings = Record<string, any>;
type Section = {
    title: string;
};
type Template = {
    title: string;
}
type System = {
    sections: Section[];
    templates: Template[];
}

let settings = writable < Settings > ({});
let system = writable < System > ({
    sections: [{
        title: 'Section'
    }],
    templates: [],
});


async function loadSettings() {
    let response = await req.get('/settings');

    if (response.ok) {
        settings.set(response.data);
    } else {
        throw new Error('Fatal error while requesting settings');
    }
}

async function boot() {
    let response = await req.get('/boot');

    if (response.ok) {
        system.update(sys => {
            sys.templates = response.data.templates;

            return sys;
        });
    } else {
        throw new Error('Fatal error while requesting settings');
    }
}

function getSettings(): Settings {
    return get(settings);
}

export {
    loadSettings,
    boot,
    getSettings,
    settings,
    system,
}
