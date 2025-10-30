export {}; // ensure this file is a module

declare global {
	interface Window {
		CMS_BASE_PATH: string;
	}
}
