<?php

declare(strict_types=1);

use Plow\Detection\ProjectRoot;
use Plow\Provider\Diagnostic\ProviderDiagnostic;
use Plow\Result\ResultStatus;
use Plow\Task\Task;
use Plow\Task\TaskMode;
use Plow\Task\TaskRequest;

it('exposes its provider name', function (): void {
    expect(makeSkeletonProvider()->name())->toBe('skeleton');
});

it('exposes the format task', function (): void {
    expect(makeSkeletonProvider()->task()->equals(Task::fromString('format')))->toBeTrue();
});

it('supports only its own task', function (): void {
    $provider = makeSkeletonProvider();

    expect($provider->supports(Task::fromString('format')))->toBeTrue()
        ->and($provider->supports(Task::fromString('analyse')))->toBeFalse();
});

it('exposes the composer package to install', function (): void {
    expect(makeSkeletonProvider()->composerPackage())->toBe('vendor/your-tool');
});

it('is not available when the binary is missing', function (): void {
    $binaryPath = (new ProjectRoot())->path().'/vendor/bin/your-tool';
    expect($binaryPath)->not->toBeFile();

    expect(makeSkeletonProvider()->isAvailable())->toBeFalse();
});

it('is available when the binary exists', function (): void {
    $binaryPath = (new ProjectRoot())->path().'/vendor/bin/your-tool';
    file_put_contents($binaryPath, '');

    try {
        expect(makeSkeletonProvider()->isAvailable())->toBeTrue();
    } finally {
        unlink($binaryPath);
    }
});

it('diagnoses itself using the binary path and availability', function (): void {
    $diagnostic = makeSkeletonProvider()->diagnose();

    expect($diagnostic)->toBeInstanceOf(ProviderDiagnostic::class)
        ->and($diagnostic->name)->toBe('skeleton')
        ->and($diagnostic->locatedAt)->toBe((new ProjectRoot())->path().'/vendor/bin/your-tool')
        ->and($diagnostic->available)->toBeFalse();
});

it('builds the base command and reports a passed result on success', function (): void {
    $processRunner = makeFakeProcessRunner(exitCode: 0, output: 'all good', errorOutput: '');
    $provider = makeSkeletonProvider($processRunner);
    $projectRoot = new ProjectRoot();

    $result = $provider->execute(new TaskRequest(task: Task::fromString('format')));

    expect($processRunner->receivedCommand)->toBe([
        PHP_BINARY,
        $projectRoot->path().'/vendor/bin/your-tool',
    ])
        ->and($processRunner->receivedWorkingDirectory)->toBe($projectRoot->path())
        ->and($result->task->equals(Task::fromString('format')))->toBeTrue()
        ->and($result->provider)->toBe('skeleton')
        ->and($result->status)->toBe(ResultStatus::Passed)
        ->and($result->output)->toBe('all good')
        ->and($result->errorOutput)->toBe('');
});

it('reports a failed result when the process exits with a non-zero code', function (): void {
    $processRunner = makeFakeProcessRunner(exitCode: 1, output: '', errorOutput: 'boom');
    $provider = makeSkeletonProvider($processRunner);

    $result = $provider->execute(new TaskRequest(task: Task::fromString('format')));

    expect($result->status)->toBe(ResultStatus::Failed)
        ->and($result->errorOutput)->toBe('boom');
});

it('appends the dry-run flag in dry-run mode', function (): void {
    $processRunner = makeFakeProcessRunner();
    $provider = makeSkeletonProvider($processRunner);

    $provider->execute(new TaskRequest(task: Task::fromString('format'), mode: TaskMode::DryRun));

    expect($processRunner->receivedCommand)->toContain('--dry-run')
        ->and(end($processRunner->receivedCommand))->toBe('--dry-run');
});

it('does not append the dry-run flag in apply mode', function (): void {
    $processRunner = makeFakeProcessRunner();
    $provider = makeSkeletonProvider($processRunner);

    $provider->execute(new TaskRequest(task: Task::fromString('format'), mode: TaskMode::Apply));

    expect($processRunner->receivedCommand)->not->toContain('--dry-run');
});

it('forwards extra arguments to the command before the dry-run flag', function (): void {
    $processRunner = makeFakeProcessRunner();
    $provider = makeSkeletonProvider($processRunner);

    $provider->execute(new TaskRequest(
        task: Task::fromString('format'),
        mode: TaskMode::DryRun,
        extraArguments: ['--config', 'custom.json'],
    ));

    expect($processRunner->receivedCommand)->toBe([
        PHP_BINARY,
        (new ProjectRoot())->path().'/vendor/bin/your-tool',
        '--config',
        'custom.json',
        '--dry-run',
    ]);
});
