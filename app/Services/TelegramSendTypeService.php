<?php

namespace App\Services;

use App\ValueObjects\DataHub;
use App\ValueObjects\TelegramSendType;

class TelegramSendTypeService
{
    public function generate(DataHub $dataHub): TelegramSendType
    {
        if ($dataHub->getTelegramPayload()->photo) {
            return TelegramSendType::SEND_PHOTO;
        } else if ($dataHub->getTelegramPayload()->media) {
            return TelegramSendType::SEND_MEDIA_GROUP;
        } else if ($dataHub->getTelegramPayload()->animation) {
            return TelegramSendType::SEND_ANIMATION;
        } else if ($dataHub->getTelegramPayload()->video) {
            return TelegramSendType::SEND_VIDEO;
        }

        return TelegramSendType::SEND_MESSAGE;
    }
}
