<?php

namespace RetailCrm\DeliveryModuleBundle\Controller;

use RetailCrm\DeliveryModuleBundle\Model\Account;
use RetailCrm\DeliveryModuleBundle\Model\AccountManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AccountValueResolver implements ArgumentValueResolverInterface
{
    /** @var AccountManagerInterface */
    private $accountManager;

    public function __construct(AccountManagerInterface $accountManager)
    {
        $this->accountManager = $accountManager;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return Account::class === $argument->getType() || is_subclass_of($argument->getType(), Account::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $clientId = $request->get('clientId');
        if (empty($clientId)) {
            throw new AccessDeniedHttpException('Parameter "clientId" is required');
        }

        $account = $this->accountManager->findAccountByClientId($clientId);
        if (null === $account) {
            throw new NotFoundHttpException('Account not found');
        }

        yield $account;
    }
}
