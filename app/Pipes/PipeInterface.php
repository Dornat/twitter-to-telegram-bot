<?php

namespace App\Pipes;

use App\ValueObjects\DataHub;

interface PipeInterface
{
    public function handle(DataHub $dataHub): mixed;

    public function setNext(PipeInterface $pipe): PipeInterface;
}
