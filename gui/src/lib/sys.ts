import req from './req';

interface Settings {
    locales: string[];
    locale: string;
    debug: boolean;
    env: string;
    csrfToken: string;
}

interface Section {
    title: string;
}

interface Template {
    title: string;
}

class System {
    path: string;
    settings: Settings;
    sections: Section[];
    templates: Template[];

    async loadSettings() {
        let response = await req.get('settings');

        if (response.ok) {
            this.settings = response.data as Settings;
        } else {
            throw new Error('Fatal error while requesting settings');
        }
    }

    async boot() {
        let response = await req.get('boot');

        if (response.ok) {
            const data = response.data;

            this.templates = data.templates as Template[];
            this.sections = data.sections as Section[];
        } else {
            throw new Error('Fatal error while requesting settings');
        }
    }
}

const system = new System();

export default system;
