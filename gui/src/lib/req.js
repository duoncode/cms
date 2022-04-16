import { success, error } from './state';
import { settings } from './settings';


const domain = `${window.location.protocol}//${window.location.host}`;

// During development the pathname should be emtpy.
// On the PHP side we use /panel in debug mode so we need to use it here as well.
const panelPath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
const panelApi = panelPath === '' ? '/panel/api' : `${panelPath}/api`;


class FetchError extends Error {
    constructor(data) {
        super('Fetch Error');
        this.data = data;
    }
}

function getDefaultOptions() {
    const headers = {
        'X-Requested-With': 'xmlhttprequest',
    };

    if (settings) {
        headers['X-CSRF-Token'] = settings.csrfToken;
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

function getBodyOptions(method, data) {
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

async function fetchit(path, params, options) {
    let url = new URL(`${panelApi}${path}`, domain);

    if (params) {
        // dynamically append GET params when value is set
        Object.keys(params).forEach((key) => {
            if (params[key]) url.searchParams.append(key, params[key]);
        });
    }
    let response = await fetch(url, options);

    if (response.status >= 400 && response.status < 800) {
        let message;

        // Unauthorized access, user needs to log in
        if (response.status === 401) {
            window.location.replace('/login');
        }

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
        throw new FetchError(message);
    }

    return response;
}

async function get(url, params) {
    const options = getBodyOptions('GET');

    return fetchit(url, params, options);
}

async function post(url, data = {}) {
    const options = getBodyOptions('POST', data);

    return fetchit(url, {}, options);
}

async function put(url, data = {}) {
    const options = getBodyOptions('PUT', data);

    return fetchit(url, {}, options);
}

async function del(url) {
    const options = getBodyOptions('DELETE');

    return fetchit(url, {}, options);
}

async function save(url, data, callback) {
    try {
        let response = await put(url, data);
        let json = await response.json();

        success(json);

        if (callback) {
            callback(json);
        }
    } catch (e) {
        if (e instanceof FetchError) {
            error(e.data);
        } else {
            throw e;
        }
    }
}

async function create(url, data, callback) {
    try {
        let response = await post(url, data);
        let json = await response.json();

        success(json);
        response = await get(`${url}/${json.uid}`);
        callback(await response.json());
    } catch (e) {
        if (e instanceof FetchError) {
            error(e.data);
        } else {
            throw e;
        }
    }
}

async function remove(url, callback) {
    try {
        let response = await del(url);
        let json = await response.json();
        success(json.message);

        if (callback) {
            callback(json);
        }
    } catch (e) {
        if (e instanceof FetchError) {
            error(e.data);
        } else {
            throw e;
        }
    }
}

export default {
    post,
    get,
    put,
    del,
    save,
    create,
    remove,
    FetchError,
};
