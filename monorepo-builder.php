<?php

declare(strict_types=1);

use App\Monorepo\ReleaseWorker\ChangelogLinkerDumpReleaseWorker;
use App\Monorepo\ReleaseWorker\ChangelogLinkerLinkReleaseWorker;
use App\Monorepo\ReleaseWorker\PushNextDevReleaseWorker;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // Things to add to root composer on merge
    $parameters->set(
        Option::DATA_TO_APPEND,
        [
            'autoload-dev' => [
                'psr-4' => [
                    'App\\' => 'src/',
                ],
            ],
            'require-dev' => [
                "roave/security-advisories" => "dev-master",
                "symplify/monorepo-builder" => "^9.0",
            ],
        ]
    );

    $parameters->set(Option::PACKAGE_ALIAS_FORMAT, '<major>.<minor>.x-dev');
    $parameters->set(Option::PACKAGE_DIRECTORIES_EXCLUDES, ['docs']);

    $services = $containerConfigurator->services();

    // Release workers - in order to execute
    $services->set(UpdateReplaceReleaseWorker::class);
    $services->set(SetCurrentMutualDependenciesReleaseWorker::class);

    $services->set(ChangelogLinkerDumpReleaseWorker::class);
    $services->set(ChangelogLinkerLinkReleaseWorker::class);
    $services->set(AddTagToChangelogReleaseWorker::class);

    $services->set(TagVersionReleaseWorker::class);
    $services->set(PushTagReleaseWorker::class);
    $services->set(SetNextMutualDependenciesReleaseWorker::class);
    $services->set(UpdateBranchAliasReleaseWorker::class);
    $services->set(PushNextDevReleaseWorker::class);
};