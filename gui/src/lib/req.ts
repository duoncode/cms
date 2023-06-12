import { success, error } from './state';
import sys from './sys';

const domain = `${window.location.protocol}//${window.location.host}`;

const panelApi = `${__CONIA_CONFIG__.panelPath}/api/`;

class Response {
    constructor(public ok: boolean, public data: any) { }
}

type Method = 'GET' | 'POST' | 'PUT' | 'DELETE';
type Headers = {
    'X-Requested-With': 'xmlhttprequest';
    'X-CSRF-Token'?: string;
};

function getDefaultOptions(): RequestInit {
    const headers: Headers = {
        'X-Requested-With': 'xmlhttprequest',
    };

    if (sys.settings) {
        headers['X-CSRF-Token'] = sys.settings.csrfToken;
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
    let options = Object.assign(getDefaultOptions(), { method });

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
    let url = new URL(`${panelApi}${path}`, domain);

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
