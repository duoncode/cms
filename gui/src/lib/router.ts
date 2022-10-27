import type { SvelteComponent } from 'svelte';
import { wrap } from 'svelte-spa-router/wrap';
import type { AsyncSvelteComponent, RoutePrecondition, WrappedComponent } from 'svelte-spa-router'

/* Duplicated from svelte-spa-router as it is not exported there */
interface WrapOptions {
    component?: typeof SvelteComponent
    asyncComponent?: AsyncSvelteComponent
    loadingComponent?: typeof SvelteComponent
    loadingParams?: object
    userData?: object
    props?: object
    conditions?: RoutePrecondition[] | RoutePrecondition
}
type Target = typeof SvelteComponent | WrapOptions;
type Component = typeof SvelteComponent | WrappedComponent;
type RouteMap = Map<string, Component>;


export default class Router {
    protected routes: RouteMap;

    constructor() {
        this.routes = new Map();
    }

    wrap(target: Target): Component {
        if ('component' in target || 'asyncComponent' in target) {
            return wrap(target);
        }

        return target as typeof SvelteComponent;
    }

    add(pattern: string, target: Target) {
        this.routes.set(pattern, this.wrap(target));
    }

    get(): RouteMap {
        return this.routes;
    }
}
