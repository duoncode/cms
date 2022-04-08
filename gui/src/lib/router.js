import { wrap } from 'svelte-spa-router/wrap';
import { apps } from '../../lib/registry';

export default class Router {
    constructor() {
        this.routes = new Map();
    }

    settings(options) {
        if (options.component || options.asyncComponent) {
            return wrap(options);
        }

        return options;
    }

    add(pattern, options) {
        this.routes.set(pattern, this.settings(options));
    }

    get() {
        let plugins = new Map();

        apps.all().map((app) => {
            app.pages.map((page) => {
                plugins.set(page.route, page.component);
            });
        });

        return new Map([...this.routes, ...plugins]);
    }
}
