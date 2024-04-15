<?php declare(strict_types=1);

namespace Shopware\Deployment\Tests\Command;

use Shopware\Deployment\Command\RunCommand;
use PHPUnit\Framework\TestCase;
use Shopware\Deployment\Services\HookExecutor;
use Shopware\Deployment\Services\InstallationManager;
use Shopware\Deployment\Services\ShopwareState;
use Shopware\Deployment\Services\UpgradeManager;
use Symfony\Component\Console\Tester\CommandTester;

class RunCommandTest extends TestCase
{
    public function testInstall(): void
    {
        $state = $this->createMock(ShopwareState::class);
        $state
            ->expects(static::once())
            ->method('isInstalled')
            ->willReturn(false);

        $hookExecutor = $this->createMock(HookExecutor::class);
        $hookExecutor
            ->expects(static::exactly(2))
            ->method('execute');

        $installationManager = $this->createMock(InstallationManager::class);
        $installationManager
            ->expects(static::once())
            ->method('run');

        $command = new RunCommand(
            $state,
            $installationManager,
            $this->createMock(UpgradeManager::class),
            $hookExecutor,
        );

        $tester = new CommandTester($command);
        $tester->execute([]);
    }

    public function testUpdate(): void
    {
        $state = $this->createMock(ShopwareState::class);
        $state
            ->expects(static::once())
            ->method('isInstalled')
            ->willReturn(true);

        $hookExecutor = $this->createMock(HookExecutor::class);
        $hookExecutor
            ->expects(static::exactly(2))
            ->method('execute');

        $installationManager = $this->createMock(InstallationManager::class);
        $installationManager
            ->expects(static::never())
            ->method('run');

        $upgradeManager = $this->createMock(UpgradeManager::class);
        $upgradeManager
            ->expects(static::once())
            ->method('run');

        $command = new RunCommand(
            $state,
            $installationManager,
            $upgradeManager,
            $hookExecutor,
        );

        $tester = new CommandTester($command);
        $tester->execute([]);
    }
}
