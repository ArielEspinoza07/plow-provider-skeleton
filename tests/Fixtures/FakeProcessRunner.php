<?php

declare(strict_types=1);

namespace Vendor\PlowProviderSkeleton\Tests\Fixtures;

use Closure;
use Plow\Contracts\Execution\ProcessRunnerInterface;
use Plow\Execution\ProcessResult;

final class FakeProcessRunner implements ProcessRunnerInterface
{
    /** @var list<string>|null */
    public ?array $receivedCommand = null;

    public ?string $receivedWorkingDirectory = null;

    public ?Closure $receivedOnOutput = null;

    public function __construct(
        private readonly ProcessResult $result = new ProcessResult(0, '', ''),
    ) {}

    public function run(array $command, ?string $workingDirectory = null, ?Closure $onOutput = null): ProcessResult
    {
        $this->receivedCommand = $command;
        $this->receivedWorkingDirectory = $workingDirectory;
        $this->receivedOnOutput = $onOutput;

        $onOutput?->__invoke('fake output line');

        return $this->result;
    }
}
