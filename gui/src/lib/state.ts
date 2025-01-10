import { writable, derived, type Writable } from 'svelte/store';
import toast from '$lib/toast';
import type { Node } from '$types/data';
import type { Field } from '$types/fields';

const pristine = writable(true);
const dirty = derived(pristine, $pristine => !$pristine);
const currentNode: Writable<null | Node> = writable(null);
const currentFields: Writable<null | Field[]> = writable(null);

function inIframe(): boolean {
	try {
		return window.self !== window.top;
	} catch (_) {
		return true;
	}
}

function setDirty() {
	pristine.set(false);

	if (window.top && inIframe()) {
		window.top.postMessage('cms-dirty', '*');
	}
}

function setPristine() {
	pristine.set(true);

	if (window.top && inIframe()) {
		window.top.postMessage('cms-pristine', '*');
	}
}

function success(message: string) {
	setPristine();
	toast.reset();

	if (message) {
		toast.add({
			...(Object(message) === message ? message : { message }),
			kind: 'success',
		});
	}
}

function error(message: string) {
	toast.add({
		...(Object(message) === message ? message : { message }),
		kind: 'error',
	});
}

export { pristine, dirty, setDirty, setPristine, success, error, currentNode, currentFields };
