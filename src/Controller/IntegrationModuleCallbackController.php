<?php

namespace RetailCrm\DeliveryModuleBundle\Controller;

use RetailCrm\DeliveryModuleBundle\Model\Account;
use RetailCrm\DeliveryModuleBundle\Model\AccountManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;

class IntegrationModuleCallbackController extends AbstractController
{
    protected const DATA_FORMAT = 'json';

    /** @var DecoderInterface */
    protected $decoder;

    /** @var AccountManagerInterface */
    protected $accountManager;

    public function __construct(DecoderInterface $decoder, AccountManagerInterface $accountManager)
    {
        $this->decoder = $decoder;
        $this->accountManager = $accountManager;
    }

    public function onActivity(Request $request, Account $account): JsonResponse
    {
        $activity = $request->request->get('activity');
        if (empty($activity)) {
            throw new BadRequestHttpException('Parameter "activity" is required');
        }

        try {
            $decodedActivity = $this->decoder->decode($activity, self::DATA_FORMAT);
        } catch (SerializerException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        $systemUrl = $request->request->get('systemUrl');
        if (empty($systemUrl)) {
            throw new BadRequestHttpException('Parameter "systemUrl" is required');
        }

        $account
            ->setActive((bool) $decodedActivity['active'])
            ->setFrozen((bool) $decodedActivity['freeze'])
            ->setCrmUrl($systemUrl)
        ;

        $this->accountManager->saveAccount($account);

        return $this->json(['success' => true]);
    }
}
