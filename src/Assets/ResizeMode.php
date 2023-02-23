<?php

declare(strict_types=1);

namespace Conia\Core\Assets;

enum ResizeMode
{
    case Crop;
    case Fit;
    case FreeCrop;
    case Height;
    case LongSide;
    case Resize;
    case ShortSide;
    case Width;
}
