<?php

namespace App\ValueObjects;

enum TwitterMediaType: string
{
    case ANIMATED_GIF = 'animated_gif';
    case PHOTO = 'photo';
    case VIDEO = 'video';
}
