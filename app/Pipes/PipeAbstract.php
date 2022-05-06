<?php

namespace App\Pipes;

use App\ValueObjects\DataHub;

abstract class PipeAbstract implements PipeInterface
{
    private ?PipeInterface $nextPipe = null;

    public function handle(DataHub $dataHub): mixed
    {
        if ($this->nextPipe) {
            return $this->nextPipe->handle($dataHub);
        }
        return $dataHub;
    }

    public function setNext(PipeInterface $pipe): PipeInterface
    {
        $this->nextPipe = $pipe;
        return $pipe;
    }
}
