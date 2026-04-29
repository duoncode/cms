<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

/**
 * @psalm-type SessionOptions = array{
 *     cookie_httponly: bool,
 *     cookie_secure: bool,
 *     cookie_lifetime: int<0, max>,
 *     gc_maxlifetime: positive-int,
 *     cache_expire: positive-int,
 *     ...<string, mixed>
 * }
 *
 * @psalm-type MimeMap = array<non-empty-string, non-empty-list<non-empty-string>>
 *
 * @psalm-type BuiltinConfig = array{
 *     'app.name': non-empty-string,
 *     'app.debug': bool,
 *     'app.env': string,
 *     'app.secret': ?non-empty-string,
 *
 *     'path.root': non-empty-string,
 *     'path.public': non-empty-string,
 *     'path.prefix': string,
 *     'path.assets': non-empty-string,
 *     'path.cache': non-empty-string,
 *     'path.views': non-empty-string,
 *     'path.panel': non-empty-string,
 *     'path.api': ?non-empty-string,
 *
 *     'panel.theme': list<non-empty-string>,
 *     'panel.logo': ?non-empty-string,
 *
 *     'error.enabled': bool,
 *     'error.renderer': null|class-string<\Duon\Error\Renderer>|\Duon\Error\Renderer,
 *     'error.trusted': list<class-string>,
 *     'error.views': null|non-empty-string|list<non-empty-string>,
 *     'error.whoops': bool,
 *
 *     'icons.local.paths': list<non-empty-string>,
 *     'icons.iconify.base_url': non-empty-string,
 *     'icons.iconify.timeout': positive-int,
 *     'icons.iconify.user_agent': non-empty-string,
 *
 *     'db.dsn': ?non-empty-string,
 *     'db.sql': list<non-empty-string>,
 *     'db.migrations': list<non-empty-string>,
 *     'db.print': bool,
 *     'db.options': array<string, mixed>,
 *
 *     'session.enabled': bool,
 *     'session.options': SessionOptions,
 *     'session.handler': ?\SessionHandlerInterface,
 *
 *     'media.fileserver': null|'apache'|'nginx',
 *
 *     'upload.mimetypes.file': MimeMap,
 *     'upload.mimetypes.image': MimeMap,
 *     'upload.mimetypes.video': MimeMap,
 *     'upload.maxsize': positive-int,
 *
 *     'password.entropy': positive-float,
 *     'password.algorithm': int|string|null
 * }
 *
 * @psalm-type BuiltinConfigInput = array{
 *     'app.name'?: string,
 *     'app.debug'?: bool|string|int,
 *     'app.env'?: string,
 *     'app.secret'?: null|string,
 *     'path.root'?: string,
 *     'path.public'?: string,
 *     'path.prefix'?: string,
 *     'path.assets'?: string,
 *     'path.cache'?: string,
 *     'path.views'?: string,
 *     'path.panel'?: string,
 *     'path.api'?: null|string,
 *     'panel.theme'?: null|string|list<string>,
 *     'panel.logo'?: null|string,
 *     'error.enabled'?: bool|string|int,
 *     'error.renderer'?: mixed,
 *     'error.trusted'?: list<class-string>,
 *     'error.views'?: null|string|list<string>,
 *     'error.whoops'?: bool|string|int,
 *     'icons.local.paths'?: string|list<string>,
 *     'icons.iconify.base_url'?: string,
 *     'icons.iconify.timeout'?: int|string,
 *     'icons.iconify.user_agent'?: string,
 *     'db.dsn'?: null|string,
 *     'db.sql'?: string|list<string>,
 *     'db.migrations'?: string|list<string>,
 *     'db.print'?: bool|string|int,
 *     'db.options'?: array<string, mixed>,
 *     'session.enabled'?: bool|string|int,
 *     'session.options'?: array<string, mixed>,
 *     'session.handler'?: ?\SessionHandlerInterface,
 *     'media.fileserver'?: null|'apache'|'nginx',
 *     'upload.mimetypes.file'?: MimeMap,
 *     'upload.mimetypes.image'?: MimeMap,
 *     'upload.mimetypes.video'?: MimeMap,
 *     'upload.maxsize'?: int|string,
 *     'password.entropy'?: float|int|string,
 *     'password.algorithm'?: int|string|null
 * }
 */
final class Types {}
