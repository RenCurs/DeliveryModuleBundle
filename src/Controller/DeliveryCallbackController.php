<?php

namespace RetailCrm\DeliveryModuleBundle\Controller;

use RetailCrm\DeliveryModuleBundle\DeliveryService\DeliveryAdapterInterface;
use RetailCrm\DeliveryModuleBundle\DeliveryService\Exception\BadRequestException;
use RetailCrm\DeliveryModuleBundle\DeliveryService\Exception\DeliveryNotFoundException;
use RetailCrm\DeliveryModuleBundle\DeliveryService\Exception\DeliveryServiceUnavailableException;
use RetailCrm\DeliveryModuleBundle\DeliveryService\Exception\ExceptionInterface as DeliveryServiceException;
use RetailCrm\DeliveryModuleBundle\DeliveryService\Exception\ShipmentNotFound;
use RetailCrm\DeliveryModuleBundle\DeliveryService\PlateAdapterInterface;
use RetailCrm\DeliveryModuleBundle\DeliveryService\ShipmentAdapterInterface;
use RetailCrm\DeliveryModuleBundle\DeliveryService\TariffAdapterInterface;
use RetailCrm\DeliveryModuleBundle\Model\Account;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;

class DeliveryCallbackController extends AbstractController
{
    protected const DATA_FORMAT = 'json';

    /** @var DecoderInterface */
    protected $decoder;

    /** @var TariffAdapterInterface|null */
    protected $tariffAdapter;

    /** @var DeliveryAdapterInterface|null */
    protected $deliveryAdapter;

    /** @var ShipmentAdapterInterface|null */
    protected $shipmentAdapter;

    /** @var PlateAdapterInterface|null */
    protected $plateAdapter;

    public function __construct(
        DecoderInterface $decoder,
        TariffAdapterInterface $tariffAdapter = null,
        DeliveryAdapterInterface $deliveryAdapter = null,
        ShipmentAdapterInterface $shipmentAdapter = null,
        PlateAdapterInterface $plateAdapter = null
    ) {
        $this->decoder = $decoder;
        $this->tariffAdapter = $tariffAdapter;
        $this->deliveryAdapter = $deliveryAdapter;
        $this->shipmentAdapter = $shipmentAdapter;
        $this->plateAdapter = $plateAdapter;
    }

    public function onTariffList(Request $request, Account $account): JsonResponse
    {
        if (null === $this->tariffAdapter) {
            throw new \LogicException('Method is not configured');
        }

        try {
            $result = $this->tariffAdapter->getTariffList($account);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        } catch (DeliveryServiceUnavailableException $e) {
            throw new ServiceUnavailableHttpException($e->getRetryAfter(), $e->getMessage(), $e);
        } catch (DeliveryServiceException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error', $e);
        }

        return $this->json(['success' => true, 'result' => $result]);
    }

    public function onCalculate(Request $request, Account $account): JsonResponse
    {
        if (null === $this->tariffAdapter) {
            throw new \LogicException('Method is not configured');
        }

        $calculate = $request->request->get('calculate');
        if (empty($calculate)) {
            throw new BadRequestHttpException('Parameter "calculate" is required');
        }

        try {
            $decodedCalculate = $this->decoder->decode($calculate, self::DATA_FORMAT);
        } catch (SerializerException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        try {
            $result = $this->tariffAdapter->calculate($account, $decodedCalculate);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        } catch (DeliveryServiceUnavailableException $e) {
            throw new ServiceUnavailableHttpException($e->getRetryAfter(), $e->getMessage(), $e);
        } catch (DeliveryServiceException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error', $e);
        }

        return $this->json(['success' => true, 'result' => $result]);
    }

    public function onSave(Request $request, Account $account): JsonResponse
    {
        if (null === $this->deliveryAdapter) {
            throw new \LogicException('Method is not configured');
        }

        $save = $request->request->get('save');
        if (empty($save)) {
            throw new BadRequestHttpException('Parameter "save" is required');
        }

        try {
            $decodedSave = $this->decoder->decode($save, self::DATA_FORMAT);
        } catch (SerializerException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        try {
            $result = $this->deliveryAdapter->createDelivery($account, $decodedSave);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        } catch (DeliveryServiceUnavailableException $e) {
            throw new ServiceUnavailableHttpException($e->getRetryAfter(), $e->getMessage(), $e);
        } catch (DeliveryServiceException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error', $e);
        }

        return $this->json(['success' => true, 'result' => $result]);
    }

    public function onGet(Request $request, Account $account): JsonResponse
    {
        if (null === $this->deliveryAdapter) {
            throw new \LogicException('Method is not configured');
        }

        $deliveryId = $request->query->get('deliveryId');
        if (empty($deliveryId)) {
            throw new BadRequestHttpException('Parameter "deliveryId" is required');
        }

        try {
            $result = $this->deliveryAdapter->getDelivery($account, $deliveryId);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        } catch (DeliveryNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        } catch (DeliveryServiceUnavailableException $e) {
            throw new ServiceUnavailableHttpException($e->getRetryAfter(), $e->getMessage(), $e);
        } catch (DeliveryServiceException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error', $e);
        }

        return $this->json(['success' => true, 'result' => $result]);
    }

    public function onDelete(Request $request, Account $account): JsonResponse
    {
        if (null === $this->deliveryAdapter) {
            throw new \LogicException('Method is not configured');
        }

        $delete = $request->request->get('delete');
        if (empty($delete)) {
            throw new BadRequestHttpException('Parameter "delete" is required');
        }

        try {
            $decodedDelete = $this->decoder->decode($delete, self::DATA_FORMAT);
        } catch (SerializerException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        try {
            $success = $this->deliveryAdapter->deleteDelivery($account, $decodedDelete);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        } catch (DeliveryNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        } catch (DeliveryServiceUnavailableException $e) {
            throw new ServiceUnavailableHttpException($e->getRetryAfter(), $e->getMessage(), $e);
        } catch (DeliveryServiceException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error', $e);
        }

        return $this->json(['success' => $success]);
    }

    public function onShipmentPointList(Request $request, Account $account): JsonResponse
    {
        if (null === $this->shipmentAdapter) {
            throw new \LogicException('Method is not configured');
        }

        $query = [
            'country' => $request->query->get('country'),
            'region' => $request->query->get('region'),
            'regionId' => $request->query->getInt('regionId'),
            'city' => $request->query->get('city'),
            'cityId' => $request->query->getInt('cityId'),
        ];

        try {
            $result = $this->shipmentAdapter->getShipmentPointList($account, $query);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        } catch (DeliveryServiceUnavailableException $e) {
            throw new ServiceUnavailableHttpException($e->getRetryAfter(), $e->getMessage(), $e);
        } catch (DeliveryServiceException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error', $e);
        }

        return $this->json(['success' => true, 'result' => $result]);
    }

    public function onShipmentSave(Request $request, Account $account): JsonResponse
    {
        if (null === $this->shipmentAdapter) {
            throw new \LogicException('Method is not configured');
        }

        $shipmentSave = $request->request->get('shipmentSave');
        if (empty($shipmentSave)) {
            throw new BadRequestHttpException('Parameter "shipmentSave" is required');
        }

        try {
            $decodedShipmentSave = $this->decoder->decode($shipmentSave, self::DATA_FORMAT);
        } catch (SerializerException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        try {
            $result = $this->shipmentAdapter->createShipment($account, $decodedShipmentSave);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        } catch (DeliveryServiceUnavailableException $e) {
            throw new ServiceUnavailableHttpException($e->getRetryAfter(), $e->getMessage(), $e);
        } catch (DeliveryServiceException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error', $e);
        }

        return $this->json(['success' => true, 'result' => $result]);
    }

    public function onShipmentDelete(Request $request, Account $account): JsonResponse
    {
        if (null === $this->shipmentAdapter) {
            throw new \LogicException('Method is not configured');
        }

        $shipmentDelete = $request->request->get('shipmentDelete');
        if (empty($shipmentDelete)) {
            throw new BadRequestHttpException('Parameter "shipmentDelete" is required');
        }

        try {
            $decodedShipmentDelete = $this->decoder->decode($shipmentDelete, self::DATA_FORMAT);
        } catch (SerializerException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        try {
            $success = $this->shipmentAdapter->deleteShipment($account, $decodedShipmentDelete);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        } catch (ShipmentNotFound $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        } catch (DeliveryServiceUnavailableException $e) {
            throw new ServiceUnavailableHttpException($e->getRetryAfter(), $e->getMessage(), $e);
        } catch (DeliveryServiceException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error', $e);
        }

        return $this->json(['success' => $success]);
    }

    public function onPrint(Request $request, Account $account): Response
    {
        if (null === $this->plateAdapter) {
            throw new \LogicException('Method is not configured');
        }

        $print = $request->request->get('print');
        if (empty($print)) {
            throw new BadRequestHttpException('Parameter "print" is required');
        }

        try {
            $decodedPrint = $this->decoder->decode($print, self::DATA_FORMAT);
        } catch (SerializerException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        try {
            $printedPlates = $this->plateAdapter->getPrintedPlates($account, $decodedPrint);
        } catch (BadRequestException $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        } catch (DeliveryServiceUnavailableException $e) {
            throw new ServiceUnavailableHttpException($e->getRetryAfter(), $e->getMessage(), $e);
        } catch (DeliveryServiceException $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error', $e);
        }

        if (empty($printedPlates)) {
            throw new BadRequestHttpException('Empty printed plate list');
        }

        if (1 === count($printedPlates)) {
            return new Response(reset($printedPlates), 200, ['Content-Type' => 'application/pdf']);
        }

        $tmpFilename = tempnam(sys_get_temp_dir(), 'zip');
        if (!$tmpFilename) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to create temp file for zip archive');
        }

        $archive = new \ZipArchive();
        $archive->open($tmpFilename, \ZipArchive::CREATE);
        foreach ($printedPlates as $filename => $printedPlate) {
            $archive->addFromString($filename, $printedPlate);
        }
        $archive->close();

        $content = file_get_contents($tmpFilename);
        if (!$content) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to read zip archive');
        }

        unlink($tmpFilename);

        return new Response($content, 200, ['Content-Type' => 'application/zip']);
    }
}
