<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

/** @psalm-import-type MimeMap from Types */
final readonly class Upload
{
	/**
	 * @param MimeMap $file
	 * @param MimeMap $image
	 * @param MimeMap $video
	 * @param positive-int $maxSize
	 */
	public function __construct(
		public array $file,
		public array $image,
		public array $video,
		public int $maxSize,
	) {}
}
