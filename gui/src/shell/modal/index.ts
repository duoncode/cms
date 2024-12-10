import type { Component } from 'svelte';

import ModalBody from './ModalBody.svelte';
import ModalFooter from './ModalFooter.svelte';
import ModalHeader from './ModalHeader.svelte';

export { ModalBody, ModalFooter, ModalHeader };

export type ModalOptions = {
    showClose?: boolean;
};

export type ModalFunctions = {
    open: (content: Component, attributes: object, options: ModalOptions) => void;
    close: () => void;
};
