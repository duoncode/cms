import type { SvelteComponent } from 'svelte';
import type { WrappedComponent } from 'svelte-spa-router';
import { wrap } from 'svelte-spa-router/wrap';

type Component = SvelteComponent | WrappedComponent;
type RouteMap = Map<string, Component>;

export default class Router {
    protected routes: RouteMap;

    constructor() {
        this.routes = new Map();
    }

    settings(options: Component) {
        if ('component' in options || 'asyncComponent' in options) {
            return wrap(options);
        }

        return options;
    }

    add(pattern: string, options: SvelteComponent) {
        this.routes.set(pattern, this.settings(options));
    }

    get(): RouteMap {
        return this.routes;
    }
}
