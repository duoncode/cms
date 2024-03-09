<?php

declare(strict_types=1);

namespace Conia\Cms\Value;

class Video extends File
{
    public function __toString(): string
    {
        $url   = $this->url();

        return "<video controls><source src=\"{$url}\" type=\"video/mp4\"/></video>";
    }
}
