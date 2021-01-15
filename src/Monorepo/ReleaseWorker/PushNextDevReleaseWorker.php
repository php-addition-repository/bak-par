<?php

declare(strict_types=1);

namespace App\Monorepo\ReleaseWorker;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\MonorepoBuilder\Utils\VersionUtils;

final class PushNextDevReleaseWorker implements ReleaseWorkerInterface
{

    public function __construct(private ProcessRunner $processRunner, private VersionUtils $versionUtils)
    {
    }

    public function work(Version $version): void
    {
        $versionInString = $this->getVersionDev($version);

        $gitAddCommitCommand = sprintf(
            'git add . && git commit -m "open %s" && git push origin main',
            $versionInString
        );

        $this->processRunner->run($gitAddCommitCommand);
    }

    private function getVersionDev(Version $version): string
    {
        return $this->versionUtils->getNextAliasFormat($version);
    }

    public function getDescription(Version $version): string
    {
        $versionInString = $this->getVersionDev($version);

        return sprintf('Push "%s" open to remote repository', $versionInString);
    }
}
