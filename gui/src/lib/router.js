import { wrap } from 'svelte-spa-router/wrap';


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
        return this.routes;
    }
}
