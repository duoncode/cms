import { success, error } from './state';

import { browser } from '$app/environment'
import { get as getStore } from 'svelte/store';
import { system } from '$lib/sys';

let domain: string;
let panelApi: string;

if (browser) {
    domain = `${window.location.protocol}//${window.location.host}`;
    panelApi = `${window.__CONIA_CONFIG__.panelPath}/api/`;
} else {
    domain = '';
    panelApi = '';
}


class Response {
    constructor(public ok: boolean, public data: any) { }
}

type Method = 'GET' | 'POST' | 'PUT' | 'DELETE';
type Headers = {
    'X-Requested-With': 'xmlhttprequest';
    Accept: string;
    'X-CSRF-Token'?: string;
};

function getDefaultOptions(): RequestInit {
    const headers: Headers = {
        'X-Requested-With': 'xmlhttprequest',
        Accept: 'application/json',
    };

    const $system = getStore(system);

    if ($system) {
        headers['X-CSRF-Token'] = $system.csrfToken;
    }

    return {
        headers,
        mode: 'cors',
        cache: 'no-cache',
        credentials: 'same-origin',
        redirect: 'follow',
        referrerPolicy: 'no-referrer',
    };
}

function getBodyOptions(method: Method, data?: any) {
    const options = Object.assign(getDefaultOptions(), { method });

    if (data) {
        if (!(data instanceof FormData)) {
            options.body = JSON.stringify(data);
            options.headers['Content-Type'] = 'application/json';
        } else {
            options.body = data;
        }
    }

    return options;
}

async function fetchit(
    path: string,
    params: Record<string, string>,
    options: RequestInit,
) {
    const url = new URL(`${panelApi}${path}`, domain);

    if (params) {
        // dynamically append GET params when value is set
        Object.keys(params).forEach(key => {
            if (params[key]) url.searchParams.append(key, params[key]);
        });
    }
    const response = await fetch(url.href, options);

    if (response.status >= 400 && response.status < 800) {
        let message: any;

        try {
            message = await response.json();

            // The user logged out in another tab and logged in again.
            // Now the csrf token is invalid.
            if (
                response.status === 400 &&
                message.error_message === 'CSRF Error'
            ) {
                window.location.reload();
            }
        } catch {
            message = { error: 'Fatal error occured' };
        }

        return new Response(false, message);
    }

    return new Response(true, await response.json());
}

async function get(url: string, params?: Record<string, string>) {
    const options = getBodyOptions('GET');

    return fetchit(url, params, options);
}

async function post(url: string, data = {}) {
    const options = getBodyOptions('POST', data);

    return fetchit(url, {}, options);
}

async function put(url: string, data = {}) {
    const options = getBodyOptions('PUT', data);

    return fetchit(url, {}, options);
}

async function del(url: string) {
    const options = getBodyOptions('DELETE');

    return fetchit(url, {}, options);
}

export default {
    post,
    get,
    put,
    del,
};
