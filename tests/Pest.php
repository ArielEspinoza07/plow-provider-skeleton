<?php

declare(strict_types=1);

use Plow\Detection\ProjectRoot;
use Plow\Execution\ProcessResult;
use Vendor\PlowProviderSkeleton\SkeletonProvider;
use Vendor\PlowProviderSkeleton\Tests\Fixtures\FakeProcessRunner;

function makeFakeProcessRunner(
    int $exitCode = 0,
    string $output = '',
    string $errorOutput = '',
): FakeProcessRunner {
    return new FakeProcessRunner(
        new ProcessResult(
            $exitCode,
            $output,
            $errorOutput,
        ),
    );
}

function makeSkeletonProvider(
    ?FakeProcessRunner $processRunner = null,
): SkeletonProvider {
    return new SkeletonProvider(
        $processRunner ?? makeFakeProcessRunner(),
        new ProjectRoot(),
    );
}
