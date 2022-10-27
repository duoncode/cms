import req from './req';

type Settings = {
    locales: string[];
    locale: string;
    debug: boolean;
    env: string;
    csrfToken: string;
};
type Section = {
    title: string;
};
type Template = {
    title: string;
}


class System {
    path: string
    settings: Settings;
    sections: Section[];
    templates: Template[];

    constructor() {
    }

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
            let data = response.data;

            this.templates = data.templates as Template[];
            this.sections = data.sections as Section[];
        } else {
            throw new Error('Fatal error while requesting settings');
        }
    }

}


const system = new System();

export default system;
