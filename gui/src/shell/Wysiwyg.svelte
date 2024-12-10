<script lang="ts">
    import type { ModalFunctions } from '$shell/modal';

    import { getContext } from 'svelte';
    import { onMount, onDestroy, createEventDispatcher } from 'svelte';

    import { setDirty } from '$lib/state';
    import { _ } from '$lib/locale';
    import ModalLink from '$shell/modals/ModalLink.svelte';

    import { Editor } from '@tiptap/core';
    import StarterKit from '@tiptap/starter-kit';
    import BubbleMenu from '@tiptap/extension-bubble-menu';
    import DropCursor from '@tiptap/extension-dropcursor';
    import HorizontalRule from '@tiptap/extension-horizontal-rule';
    import Link from '@tiptap/extension-link';
    import Paragraph from '@tiptap/extension-paragraph';
    import SubScript from '@tiptap/extension-subscript';
    import SuperScript from '@tiptap/extension-superscript';
    import TextAlign from '@tiptap/extension-text-align';

    import IcoH1 from '$shell/icons/IcoH1.svelte';
    import IcoH2 from '$shell/icons/IcoH2.svelte';
    import IcoH3 from '$shell/icons/IcoH3.svelte';
    import IcoBold from '$shell/icons/IcoBold.svelte';
    import IcoBlockQuoteRight from '$shell/icons/IcoBlockQuoteRight.svelte';
    import IcoParagraph from '$shell/icons/IcoParagraph.svelte';
    import IcoHorizontalRule from '$shell/icons/IcoHorizontalRule.svelte';
    import IcoTextHeight from '$shell/icons/IcoTextHeight.svelte';
    import IcoItalic from '$shell/icons/IcoItalic.svelte';
    import IcoAlignLeft from '$shell/icons/IcoAlignLeft.svelte';
    import IcoAlignRight from '$shell/icons/IcoAlignRight.svelte';
    import IcoAlignCenter from '$shell/icons/IcoAlignCenter.svelte';
    import IcoAlignJustify from '$shell/icons/IcoAlignJustify.svelte';
    import IcoRemoveFormat from '$shell/icons/IcoRemoveFormat.svelte';
    import IcoSubscript from '$shell/icons/IcoSubscript.svelte';
    import IcoSuperscript from '$shell/icons/IcoSuperscript.svelte';
    import IcoStrikethrough from '$shell/icons/IcoStrikethrough.svelte';
    import IcoListUl from '$shell/icons/IcoListUl.svelte';
    import IcoListOl from '$shell/icons/IcoListOl.svelte';
    import IcoUndo from '$shell/icons/IcoUndo.svelte';
    import IcoRedo from '$shell/icons/IcoRedo.svelte';
    import IcoCode from '$shell/icons/IcoCode.svelte';
    import IcoLink from '$shell/icons/IcoLink.svelte';
    import IcoUnlink from '$shell/icons/IcoUnlink.svelte';
    import IcoDocument from '$shell/icons/IcoDocument.svelte';
    import IcoLineBreak from '$shell/icons/IcoLineBreak.svelte';

    type Props = {
        value: any;
        name: any;
        editSource?: boolean;
        required?: boolean;
        toolbar?: string;
        embed?: boolean;
    };

    let {
        value = $bindable(),
        name,
        editSource = true,
        required = false,
        toolbar = 'default',
        embed = false,
    }: Props = $props();
    let { open, close } = getContext<ModalFunctions>('modal');

    const dispatch = createEventDispatcher();

    let ref = $state();
    let bubble = $state();
    let editor = $state();
    let showSource = $state(false);
    let showDropdown = $state(false);

    function fireUpdate(html) {
        dispatch('input', html);
        setDirty();
        dispatch('dirty');
    }

    const CustomParagraph = Paragraph.extend({
        addAttributes() {
            return {
                class: {
                    default: 'default',
                },
            };
        },
    });

    const CustomLink = Link.extend({
        addAttributes() {
            return {
                ...this.parent?.(),
                target: {
                    default: undefined,
                },
                class: {
                    default: undefined,
                },
                rel: {
                    default: 'noopener noreferrer nofollow',
                },
            };
        },
    });
    const CustomHorizontalRule = HorizontalRule.extend({
        addAttributes() {
            return {
                ...this.parent?.(),
                class: {
                    default: undefined,
                },
            };
        },
    });

    onMount(() => {
        let extensions;

        if (toolbar === 'inline') {
            extensions = [
                StarterKit,
                BubbleMenu.configure({
                    element: bubble,
                }),
            ];
        } else {
            extensions = [
                StarterKit,
                CustomParagraph,
                TextAlign.configure({
                    types: ['heading', 'paragraph'],
                }),
                DropCursor,
                CustomLink.configure({
                    openOnClick: false,
                }),
                CustomHorizontalRule,
                SubScript,
                SuperScript,
            ];
        }

        editor = new Editor({
            element: ref,
            extensions,
            content: value,
            onTransaction: () => {
                // force re-render so `editor.isActive` works as expected
                editor = editor;
            },
            onUpdate: () => {
                let html = editor.getHTML();
                fireUpdate(html);
                value = html;
            },
        });
    });

    onDestroy(() => {
        if (editor) {
            editor.destroy();
        }
    });

    function changeSource(e) {
        fireUpdate(e.target.value);
        editor.commands.setContent(e.target.value);
        value = e.target.value;
    }

    function clickDropdown(fn) {
        return () => {
            if (fn) {
                fn();
            }
            showDropdown = !showDropdown;
        };
    }

    function clickToolbar(fn) {
        showDropdown = false;
        fn();
    }

    function toggleSource() {
        showSource = !showSource;
        showDropdown = false;
    }

    function addLink(url: string, blank: boolean) {
        if (url) {
            editor
                .chain()
                .focus()
                .extendMarkRange('link')
                .setLink({
                    href: url,
                    target: blank ? '_blank' : '',
                    class: undefined,
                })
                .run();
        }
    }

    function openAddLinkModal() {
        const value = editor.isActive('link') ? editor.getAttributes('link').href : '';
        const target = editor.isActive('link') ? editor.getAttributes('link').target : '';

        open(
            ModalLink,
            {
                add: addLink,
                close,
                value,
                blank: target === '_blank',
            },
            {},
        );
    }
</script>

{#if toolbar === 'inline'}
    <div
        class="wysiwyg-bubble bg-gray-600 text-white px-1 rounded"
        bind:this={bubble}>
        {#if editor}
            <button
                class="wysiwyg-toolbar-btn"
                onclick={clickToolbar(editor.chain().focus().toggleBold().run)}
                class:active={editor.isActive('bold')}>
                <IcoBold />
            </button>
            <button
                class="wysiwyg-toolbar-btn"
                onclick={clickToolbar(editor.chain().focus().toggleItalic().run)}
                class:active={editor.isActive('italic')}>
                <IcoItalic />
            </button>
            <button
                class="wysiwyg-toolbar-btn"
                onclick={clickToolbar(editor.chain().focus().toggleStrike().run)}
                class:active={editor.isActive('strike')}>
                <IcoStrikethrough />
            </button>
            <button
                class="wysiwyg-toolbar-btn"
                onclick={clickToolbar(editor.chain().focus().unsetAllMarks().run)}>
                <IcoRemoveFormat />
            </button>
        {/if}
    </div>
{/if}

<div
    class="wysiwyg wysiwyg-{toolbar}"
    class:required
    class:embed>
    {#if editor}
        {#if toolbar !== 'inline'}
            <div
                class="wysiwyg-toolbar relative z-10 {!showSource && 'flex'}"
                class:tooltip-b={embed}>
                {#if showSource}
                    <div class="wysiwyg-extras text-right">
                        <button
                            onclick={toggleSource}
                            class="wysiwyg-source-btn my-1 p-1">
                            <IcoDocument />
                            <span class="ml-1">
                                {_('Show content')}
                            </span>
                        </button>
                    </div>
                {:else}
                    <div class="relative inline-block text-left">
                        <div class="wysiwyg-dropdown">
                            <button
                                type="button"
                                class="wysiwyg-dropdown-button"
                                aria-expanded="true"
                                aria-haspopup="true"
                                onclick={clickDropdown(null)}>
                                {_('Absatz')}
                                <svg
                                    class="-mr-1 ml-2 h-5 w-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true">
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        {#if showDropdown}
                            <div
                                class="wysiwyg-dropdown-menu"
                                role="menu"
                                aria-orientation="vertical"
                                aria-labelledby="menu-button"
                                tabindex="-1">
                                <div
                                    class="py-1"
                                    role="none">
                                    <button
                                        onclick={clickDropdown(
                                            editor.chain().focus().toggleHeading({ level: 1 }).run,
                                        )}
                                        role="menuitem"
                                        tabindex="-1"
                                        class="wysiwyg-dropdown-item"
                                        class:active={editor.isActive('heading', { level: 1 })}>
                                        <IcoH1 />
                                        <span class="ml-2">
                                            {_('Überschrift Level 1')}
                                        </span>
                                    </button>
                                    <button
                                        onclick={clickDropdown(
                                            editor.chain().focus().toggleHeading({ level: 2 }).run,
                                        )}
                                        role="menuitem"
                                        tabindex="-1"
                                        class="wysiwyg-dropdown-item"
                                        class:active={editor.isActive('heading', { level: 2 })}>
                                        <IcoH2 />
                                        <span class="ml-2">
                                            {_('Überschrift Level 2')}
                                        </span>
                                    </button>
                                    <button
                                        onclick={clickDropdown(
                                            editor.chain().focus().toggleHeading({ level: 3 }).run,
                                        )}
                                        role="menuitem"
                                        tabindex="-1"
                                        class="wysiwyg-dropdown-item"
                                        class:active={editor.isActive('heading', { level: 3 })}>
                                        <IcoH3 />
                                        <span class="ml-2">
                                            {_('Überschrift Level 3')}
                                        </span>
                                    </button>
                                    <button
                                        onclick={clickDropdown(
                                            editor.chain().focus().setParagraph().run,
                                        )}
                                        role="menuitem"
                                        tabindex="-1"
                                        class="wysiwyg-dropdown-item"
                                        class:active={editor.isActive('paragraph') &&
                                            editor.getAttributes('paragraph')['class'] !== 'large'}>
                                        <IcoParagraph />
                                        <span class="ml-2">
                                            {_('Absatz')}
                                        </span>
                                    </button>
                                    <button
                                        onclick={clickDropdown(
                                            editor
                                                .chain()
                                                .focus()
                                                .setParagraph()
                                                .updateAttributes('paragraph', {
                                                    class: 'large',
                                                }).run,
                                        )}
                                        role="menuitem"
                                        tabindex="-1"
                                        class="wysiwyg-dropdown-item"
                                        class:active={editor.isActive('paragraph') &&
                                            editor.getAttributes('paragraph')['class'] === 'large'}>
                                        <IcoTextHeight />
                                        <span class="ml-2">
                                            {_('Absatz große Schrift')}
                                        </span>
                                    </button>
                                    <button
                                        onclick={clickDropdown(
                                            editor
                                                .chain()
                                                .focus()
                                                .setParagraph()
                                                .updateAttributes('paragraph', {
                                                    class: 'small',
                                                }).run,
                                        )}
                                        role="menuitem"
                                        tabindex="-1"
                                        class="wysiwyg-dropdown-item"
                                        class:active={editor.isActive('paragraph') &&
                                            editor.getAttributes('paragraph')['class'] === 'small'}>
                                        <IcoTextHeight />
                                        <span class="ml-2">
                                            {_('Absatz kleine Schrift')}
                                        </span>
                                    </button>
                                    <button
                                        onclick={clickDropdown(
                                            editor.chain().focus().clearNodes().run,
                                        )}
                                        role="menuitem"
                                        tabindex="-1"
                                        class="wysiwyg-dropdown-item">
                                        <IcoRemoveFormat />
                                        <span class="ml-2">
                                            {_('Format entfernen')}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        {/if}
                    </div>
                    <div class="wysiwyg-toolbar-btns flex-grow">
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Text align left')}
                            onclick={clickToolbar(editor.chain().focus().unsetTextAlign().run)}>
                            <IcoAlignLeft />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Text align center')}
                            onclick={clickToolbar(
                                editor.chain().focus().setTextAlign('center').run,
                            )}
                            class:active={editor.isActive({
                                textAlign: 'center',
                            })}>
                            <IcoAlignCenter />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Text align right')}
                            onclick={clickToolbar(editor.chain().focus().setTextAlign('right').run)}
                            class:active={editor.isActive({
                                textAlign: 'right',
                            })}>
                            <IcoAlignRight />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Justify text')}
                            onclick={clickToolbar(
                                editor.chain().focus().setTextAlign('justify').run,
                            )}
                            class:active={editor.isActive({
                                textAlign: 'justify',
                            })}>
                            <IcoAlignJustify />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Bold text')}
                            onclick={clickToolbar(editor.chain().focus().toggleBold().run)}
                            class:active={editor.isActive('bold')}>
                            <IcoBold />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Italic text')}
                            onclick={clickToolbar(editor.chain().focus().toggleItalic().run)}
                            class:active={editor.isActive('italic')}>
                            <IcoItalic />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Strike through')}
                            onclick={clickToolbar(editor.chain().focus().toggleStrike().run)}
                            class:active={editor.isActive('strike')}>
                            <IcoStrikethrough />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Bulleted list')}
                            onclick={clickToolbar(editor.chain().focus().toggleBulletList().run)}
                            class:active={editor.isActive('bulletList')}>
                            <IcoListUl />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Numbered list')}
                            onclick={clickToolbar(editor.chain().focus().toggleOrderedList().run)}
                            class:active={editor.isActive('orderedList')}>
                            <IcoListOl />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Subscript')}
                            onclick={clickToolbar(editor.chain().focus().toggleSubscript().run)}
                            class:active={editor.isActive('subscript')}>
                            <IcoSubscript />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Superscript')}
                            onclick={clickToolbar(editor.chain().focus().toggleSuperscript().run)}
                            class:active={editor.isActive('superscript')}>
                            <IcoSuperscript />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Block quote')}
                            onclick={clickToolbar(editor.chain().focus().toggleBlockquote().run)}
                            class:active={editor.isActive('blockquote')}>
                            <IcoBlockQuoteRight />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Horizontal line')}
                            onclick={clickToolbar(editor.chain().focus().setHorizontalRule().run)}>
                            <IcoHorizontalRule />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Add link to page')}
                            onclick={openAddLinkModal}>
                            <IcoLink />
                        </button>
                        {#if editor.isActive('link')}
                            <button
                                class="wysiwyg-toolbar-btn"
                                tooltip={_('Remove link')}
                                onclick={clickToolbar(editor.chain().focus().unsetLink().run)}>
                                <IcoUnlink />
                            </button>
                        {/if}
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Add a hard line break')}
                            onclick={clickToolbar(editor.chain().focus().setHardBreak().run)}>
                            <IcoLineBreak />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Remove formats')}
                            onclick={clickToolbar(editor.chain().focus().unsetAllMarks().run)}>
                            <IcoRemoveFormat />
                        </button>
                    </div>
                    <div class="wysiwyg-extras">
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Undo last action')}
                            onclick={clickToolbar(editor.chain().focus().undo().run)}>
                            <IcoUndo />
                        </button>
                        <button
                            class="wysiwyg-toolbar-btn"
                            tooltip={_('Redo last undo')}
                            onclick={clickToolbar(editor.chain().focus().redo().run)}>
                            <IcoRedo />
                        </button>
                        {#if editSource}
                            <button
                                onclick={toggleSource}
                                class="wysiwyg-source-btn ml-3">
                                <IcoCode />
                                {_('Show source')}
                            </button>
                        {/if}
                    </div>
                {/if}
            </div>
        {/if}
    {/if}

    <div
        class="wysiwyg-editor prose relative z-0"
        bind:this={ref}
        class:hide={showSource}
        onclick={() => (showDropdown = false)}>
    </div>
    <div
        class="wysiwyg-source relative z-0"
        class:hide={!showSource}>
        <textarea
            onkeyup={changeSource}
            bind:value
            class="border-0 w-full h-64 p-1 bg-gray-700 font-mono text-white">
        </textarea>
    </div>
</div>
