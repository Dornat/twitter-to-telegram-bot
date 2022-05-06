<?php

namespace App\Pipelines;

use App\Pipes\AnimatedGifPipe;
use App\Pipes\PicturePipe;
use App\Pipes\PipeInterface;
use App\Pipes\TextPipe;
use App\Pipes\VideoPipe;
use App\ValueObjects\DataHub;

class DefaultPipelineFactory implements PipelineFactoryInterface
{
    private PipeInterface $basedPipe;

    public function create(): DefaultPipelineFactory
    {
        $this->basedPipe = new TextPipe();
        $picturePipe = new PicturePipe();
        $animatedGifPipe = new AnimatedGifPipe();
        $videoPipe = new VideoPipe();

        $this->basedPipe
            ->setNext($picturePipe)
            ->setNext($animatedGifPipe)
            ->setNext($videoPipe);

        return $this;
    }

    public function run(DataHub $dataHub): mixed
    {
        return $this->basedPipe->handle($dataHub);
    }
}
