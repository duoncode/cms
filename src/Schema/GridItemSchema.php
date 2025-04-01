<?php

declare(strict_types=1);

namespace Duon\Cms\Schema;

use Duon\Sire\Schema;

class GridItemSchema extends Schema
{
	protected function rules(): void
	{
		$this->add('type', 'text', 'required', 'in:text,html,image,youtube,images,video,iframe');
		$this->add('rowspan', 'int', 'required');
		$this->add('colspan', 'int', 'required');
		$this->add('colstart', 'int');
	}

	protected function review(): void
	{
		foreach ($this->values() as $value) {
			$type = $value['type'] ?? null;

			if ($type === 'image' || $type === 'images' || $type === 'video') {
				$files = $value['files'] ?? [];

				if (count($files) > 0) {
					$fileSchema = new Schema(list: true, title: _('Grid Bild'), keepUnknown: true);
					$fileSchema->add('file', 'text', 'required');
					$fileSchema->add('title', 'text');
					$fileSchema->add('alt', 'text');

					if (!$fileSchema->validate($files)) {
						$this->addError('image', _('Grid Bild'), _('Attribute `file` nicht gefüllt.'));
					}

					continue;
				}

				$this->addError('image', _('Grid Bild'), _('Bild eingefügt aber nicht hochgeladen.'));
			} elseif ($type === 'youtube') {
				if (!($value['value'] ?? null)) {
					$this->addError('value', _('Youtube-ID'), _('Bitte gültige Youtube-ID eingeben.'));
				}

				$aspectRatioX = $value['aspectRatioX'] ?? null;

				if (!$aspectRatioX || !is_numeric($aspectRatioX)) {
					$this->addError('aspectRatioX', _('Youtube Seitenverhältnis Breite'), _('Bitte gültige Zahl eingeben.'));
				}

				$aspectRatioY = $value['aspectRatioY'] ?? null;

				if (!$aspectRatioY || !is_numeric($aspectRatioY)) {
					$this->addError('aspectRatioY', _('Youtube Seitenverhältnis Höhe'), _('Bitte gültige Zahl eingeben.'));
				}
			} elseif ($type === 'html' || $type === 'text') {
				if (!($value['value'] ?? null)) {
					$this->addError('value', _('Grid Text'), _('Bitte Textfeld ausfüllen oder Block löschen.'));
				}
			} elseif ($type === 'iframe') {
				if (!($value['value'] ?? null)) {
					$this->addError('value', _('Grid Text'), _('Bitte Iframe-Feld ausfüllen oder Block löschen.'));
				}
			}
		}
	}
}
