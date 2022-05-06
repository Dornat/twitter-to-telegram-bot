<?php

namespace App\ValueObjects;

enum TelegramSendType: string
{
    case SEND_ANIMATION = 'sendAnimation';
    case SEND_MEDIA_GROUP = 'sendMediaGroup';
    case SEND_MESSAGE = 'sendMessage';
    case SEND_PHOTO = 'sendPhoto';
    case SEND_VIDEO = 'sendVideo';
}
