<?php

declare(strict_types=1);

namespace Duon\Cms\Value;

use Duon\Cms\Assets\ResizeMode;
use Duon\Cms\Assets\Size;
use Duon\Cms\Field;
use Duon\Cms\Node\Node;
use Duon\Cms\Util\Html as HtmlUtil;
use Generator;
use Gumlet\ImageResize;

class Grid extends Value
{
	protected readonly ?Generator $preparedData;

	public function __construct(Node $node, Field\Grid $field, ValueContext $context)
	{
		parent::__construct($node, $field, $context);

		$this->preparedData = $this->prepareData($this->data);
	}

	public function __toString(): string
	{
		return $this->render();
	}

	public function json(): array
	{
		return $this->unwrap();
	}

	public function unwrap(): array
	{
		return [
			'columns' => $this->data['columns'] ?? null,
			'data' => $this->preparedData,
		];
	}

	public function image(int $index = 1): ?Image
	{
		$i = 0;

		foreach ($this->preparedData as $value) {
			if ($value->type === 'image') {
				$i++;

				if ($i === $index) {
					return (new Field\Image(
						$this->context->fieldName,
						$this->node,
						new ValueContext($this->context->fieldName, $value->data),
					))->value();
				}
			}
		}

		return null;
	}

	public function images(bool $all = false): Generator
	{
		if ($all && $this->translate) {
			foreach ($this->data['value'] ?? [] as $data) {
				foreach ($data as $value) {
					$item = new GridItem($value['type'], $value);

					if ($item->type === 'image') {
						yield (new Field\Image(
							$this->context->fieldName,
							$this->node,
							new ValueContext($this->context->fieldName, $item->data),
						))->value();
					} elseif ($item->type === 'images') {
						foreach ((new Field\Image(
							$this->context->fieldName,
							$this->node,
							new ValueContext($this->context->fieldName, $item->data),
						))->multiple()->value() as $image) {
							yield $image;
						}
					}
				}
			}
		} else {
			foreach ($this->preparedData as $item) {
				if ($item->type === 'image') {
					yield (new Field\Image(
						$this->context->fieldName,
						$this->node,
						new ValueContext($this->context->fieldName, $item->data),
					))->value();
				} elseif ($item->type === 'images') {
					foreach ((new Field\Image(
						$this->context->fieldName,
						$this->node,
						new ValueContext($this->context->fieldName, $item->data),
					))->multiple()->value() as $image) {
						yield $image;
					}
				}
			}
		}
	}

	public function hasImage(int $index = 1): bool
	{
		$i = 0;

		foreach ($this->preparedData as $value) {
			if ($value->type === 'image') {
				$i++;

				if ($i === $index) {
					return true;
				}
			}
		}

		return false;
	}

	public function excerpt(
		int $words = 30,
		string $allowedTags = '',
		int $index = 1,
	): string {
		$i = 0;

		foreach ($this->preparedData as $value) {
			if ($value->type === 'html') {
				$i++;

				if ($i === $index) {
					return HtmlUtil::excerpt($value->data['value'] ?? '', $words, $allowedTags);
				}
			}
		}

		return '';
	}

	public function columns(): int
	{
		return (int) ($this->data['columns'] ?? 12);
	}

	// Supported args:
	//
	// - prefix: All css classes are prefixed with this value. Default 'cms'
	// - tag: The tag of the container. Default 'div'
	// - maxImageWidth: The maximum width of images. Images will be resized according to colspan. Default: 1280
	// - class: An additional class added to the container
	public function render(mixed ...$args): string
	{
		$args['tag'] = $tag = $args['tag'] ?? 'div';
		$args['prefix'] = $prefix = $args['prefix'] ?? 'cms';
		$args['class'] = $class = ($args['class'] ?? '' ? ' ' . $args['class'] : '');

		$columns = $this->columns();

		$out = '<' . $tag . ' class="' . $prefix . '-grid ' . $prefix .
			'-grid-columns-' . $columns . $class . '">';

		foreach ($this->preparedData as $value) {
			$out .= $this->renderValue($prefix, $value, $args);
		}

		$out .= '</' . $tag . '>';

		return $out;
	}

	public function isset(): bool
	{
		if ($this->preparedData === null) {
			return false;
		}

		if (!($this->data['value'] ?? null)) {
			return false;
		}

		if ($this->translate) {
			return count($this->data['value'][$this->defaultLocale->id]) > 0;
		}

		return count($this->data['value']) > 0;
	}

	protected function renderValue(string $prefix, GridItem $value, array $args): string
	{
		$colspan = $prefix . '-colspan-' . $value->data['colspan'];
		$rowspan = $prefix . '-rowspan-' . $value->data['rowspan'];
		$styleClass = $value->styleClass();
		$class = $styleClass ? ' ' . $styleClass : '';

		$out = '<div class="' . $prefix . '-' . $value->type .
			' ' . $colspan . ' ' . $rowspan . $class . '">';
		$out .= match ($value->type) {
			'html' => $value->data['value'],
			'text' => $value->data['value'],
			'h1' => '<h1>' . $value->data['value'] . '</h1>',
			'h2' => '<h2>' . $value->data['value'] . '</h2>',
			'h3' => '<h3>' . $value->data['value'] . '</h3>',
			'h4' => '<h4>' . $value->data['value'] . '</h4>',
			'h5' => '<h5>' . $value->data['value'] . '</h5>',
			'h6' => '<h6>' . $value->data['value'] . '</h6>',
			'iframe' => $value->data['value'],
			'image' => $this->renderImage($value->data, $args),
			'images' => $this->renderImages($value->data, $args),
			'youtube' => $this->getValueObject(Field\Youtube::class, $value)->__toString(),
			'video' =>  $this->getValueObject(Field\Video::class, $value)->__toString(),
		};
		$out .= '</div>';

		return $out;
	}

	protected function getValueObject(string $class, GridItem $item): Value
	{
		return (new $class(
			$this->context->fieldName,
			$this->node,
			new ValueContext($this->context->fieldName, $item->data),
		))->value();
	}

	protected function renderImage(array $data, array $args): string
	{
		$file = $data['files'][0]['file'];
		$title = $data['files'][0]['title'] ?? '';
		$maxWidth = $args['maxImageWidth'] ?? 1440;
		$path = $this->assetsPath() . $file;
		$image = $this->getAssets()->image($path);
		$resized = $image->resize(
			new Size((int) ($maxWidth / $this->columns()) * (int) ($data['colspan'] ?? 12)),
			ResizeMode::Width,
			enlarge: false,
			quality: null,
		);
		$url = $resized->url(true);

		return "<img src=\"{$url}\" alt=\"{$title}\" data-path-original=\"{$path}\">";
	}

	protected function renderImages(array $data, array $args): string
	{
		$result = '';

		foreach ($data['files'] as $f) {
			$file = $f['file'];
			$title = $f['title'] ?? '';
			$path = $this->assetsPath() . $file;
			$image = $this->getAssets()->image($path);
			$resized = $image->resize(
				new Size(400, 267, cropMode: ImageResize::CROPCENTER),
				ResizeMode::Crop,
				enlarge: false,
				quality: null,
			);
			$url = $resized->url(true);

			$result .= "<div class=\"cms-grid-images-image\"><img src=\"{$url}\" alt=\"{$title}\" data-path-original=\"{$path}\"></div>";
		}

		if ($result) {
			return '<div class="cms-grid-images">' . $result . '</div>';
		}

		return '';
	}

	protected function prepareData(array $data): Generator
	{
		if ($this->translate) {
			$locale = $this->locale;

			while ($locale) {
				$fields = $data['value'][$locale->id] ?? null;

				if ($fields && count($fields) > 0) {
					break;
				}

				$locale = $locale->fallback();
			}
		} else {
			$fields = $data['value'];
		}

		foreach ($fields as $field) {
			yield new GridItem($field['type'], $field);
		}
	}
}
