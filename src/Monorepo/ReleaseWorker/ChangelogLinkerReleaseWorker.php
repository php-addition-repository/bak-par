<?php

declare( strict_types=1 );

namespace App\Monorepo\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

final class ChangelogLinkerReleaseWorker implements ReleaseWorkerInterface
{

    /**
     * @var ProcessRunner
     */
    private ProcessRunner $processRunner;

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    public function work ( Version $version ): void
    {
        $this->processRunner->run(
            'vendor/bin/changelog-linker dump-merges'
        );
        $this->processRunner->run(
            'vendor/bin/changelog-linker link'
        );
        $this->processRunner->run(
            'vendor/bin/changelog-linker cleanup'
        );
    }

    public function getDescription ( Version $version ): string {
        return 'Update CHANGELOG.md';
    }
}