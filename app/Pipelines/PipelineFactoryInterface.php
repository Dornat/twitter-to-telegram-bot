<?php

namespace App\Pipelines;

use App\ValueObjects\DataHub;

interface PipelineFactoryInterface
{
    public function create(): PipelineFactoryInterface;

    public function run(DataHub $dataHub): mixed;
}
