<?php

namespace RetailCrm\DeliveryModuleBundle\Command;

use RetailCrm\DeliveryModuleBundle\Model\Account;
use RetailCrm\DeliveryModuleBundle\Model\AccountManagerInterface;
use RetailCrm\DeliveryModuleBundle\Tracking\TrackerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TrackingCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'delivery:tracking';

    /** @var AccountManagerInterface */
    private $accountManager;

    /** @var TrackerInterface|null */
    private $tracker;

    public function __construct(
        AccountManagerInterface $accountManager,
        TrackerInterface $tracker,
        string $name = null
    ) {
        $this->accountManager = $accountManager;
        $this->tracker = $tracker;

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Delivery tracking')
            ->addArgument('accountId', InputArgument::OPTIONAL, 'Choose account, or make it for all')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 1;
        }

        $startTime = new \DateTime();

        $accountId = $input->hasArgument('accountId')
            ? (int) $input->getArgument('accountId')
            : null
        ;

        $successfulCount = $failureCount = 0;

        $accounts = $this->getActiveAccounts($accountId);

        /** @var Account $account */
        foreach ($accounts as $account) {
            try {
                $this->tracker->tracking($account);

                ++$successfulCount;
            } catch (\Exception $e) {
                $output->writeln("<error>Failed to update statuses for account {$account->getCrmUrl()}[{$account->getClientId()}]</error>");
                $output->writeln("<error>Error: {$e->getMessage()}</error>");

                ++$failureCount;
            }
        }

        $this->release();

        $output->writeln(sprintf('<info>Accounts updated successful = %s</info>', $successfulCount));
        $output->writeln(sprintf('<info>Accounts failed updates = %s</info>', $failureCount));
        $output->writeln(sprintf('<info>Memory = %s</info>', memory_get_peak_usage()));
        $output->writeln(sprintf('<info>Time of execution = %s</info>', $startTime->diff(new \DateTime())->format('%I:%S')));

        return 0;
    }

    protected function getActiveAccounts(int $accountId = null): \Generator
    {
        if (null !== $accountId) {
            $account = $this->accountManager->findActiveAccountById($accountId);

            if (null !== $account) {
                yield $account;
            } else {
                yield from [];
            }
        } else {
            return $this->accountManager->findActiveAccounts();
        }
    }
}
