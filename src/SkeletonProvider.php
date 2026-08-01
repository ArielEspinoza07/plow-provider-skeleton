<?php

declare(strict_types=1);

namespace Vendor\PlowProviderSkeleton;

use Plow\Contracts\Execution\ProcessRunnerInterface;
use Plow\Contracts\Provider\ProviderInterface;
use Plow\Detection\ProjectRoot;
use Plow\Provider\Diagnostic\ProviderDiagnostic;
use Plow\Result\ResultStatus;
use Plow\Result\TaskResult;
use Plow\Task\Task;
use Plow\Task\TaskMode;
use Plow\Task\TaskRequest;

/**
 * TODO: rename this class, and the file, to match your tool
 * (e.g. EslintProvider, PhpMdProvider).
 */
final readonly class SkeletonProvider implements ProviderInterface
{
    public function __construct(
        private ProcessRunnerInterface $processRunner,
        private ProjectRoot $projectRoot,
    ) {}

    /**
     * A short, unique, lowercase identifier — shown in `plow doctor`,
     * `plow providers`, and used as the value for `--provider=`.
     */
    public function name(): string
    {
        return 'skeleton';
    }

    /**
     * The task this provider performs. Use Task::fromString() with a
     * built-in value (format, analyse, test, refactor, audit) if your
     * tool fits an existing category, or a new custom string if it
     * genuinely doesn't — Plow's Task type is open by design.
     */
    public function task(): Task
    {
        return Task::fromString('format');
    }

    public function supports(Task $task): bool
    {
        return $task->equals($this->task());
    }

    /**
     * The Composer package `plow install` should require if this
     * provider isn't yet available.
     *
     * TODO: if your tool isn't installed via Composer (e.g. an
     * npm-based tool), widen this back to `?string` and return null.
     */
    public function composerPackage(): string
    {
        return 'vendor/your-tool';
    }

    public function isAvailable(): bool
    {
        return is_file($this->binaryPath());
    }

    public function diagnose(): ProviderDiagnostic
    {
        return new ProviderDiagnostic(
            name: $this->name(),
            locatedAt: $this->binaryPath(),
            available: $this->isAvailable(),
        );
    }

    /**
     * TODO: build the real command for your tool.
     *
     * - $request->mode: TaskMode::DryRun / TaskMode::Apply, if your tool
     *   distinguishes preview from apply (most formatters/refactoring
     *   tools do — see PintProvider for the pattern). If your tool has
     *   no such distinction (e.g. a static analyzer), ignore $request->mode.
     * - $request->extraArguments: raw args the user passed after `--`,
     *   e.g. `plow test -- --coverage` — append these to your command
     *   verbatim, don't try to interpret them.
     */
    public function execute(TaskRequest $request): TaskResult
    {
        $command = [
            PHP_BINARY,
            $this->binaryPath(),
            ...$request->extraArguments,
            ];

        if ($request->mode === TaskMode::DryRun) {
            $command[] = '--dry-run'; // TODO: use your tool's real flag
        }

        $result = $this->processRunner->run($command, $this->projectRoot->path());

        return new TaskResult(
            task: $request->task,
            provider: $this->name(),
            status: $result->successful() ? ResultStatus::Passed : ResultStatus::Failed,
            output: $result->output,
            errorOutput: $result->errorOutput,
        );
    }

    /**
     * TODO: point this at your tool's real binary path.
     */
    private function binaryPath(): string
    {
        return $this->projectRoot->path().'/vendor/bin/your-tool';
    }
}
