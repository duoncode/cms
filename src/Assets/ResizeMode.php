<?php

declare(strict_types=1);

namespace Conia\Cms\Assets;

enum ResizeMode: string
{
    case Crop = 'crop';
    case Fit = 'fit';
    case FreeCrop = 'freecrop';
    case Height = 'height';
    case LongSide = 'longside';
    case Resize = 'resize';
    case ShortSide = 'shortside';
    case Width = 'width';
}
