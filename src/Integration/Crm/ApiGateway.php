<?php

namespace RetailCrm\DeliveryModuleBundle\Integration\Crm;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\GuzzleException as GuzzleHttpException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;
use RetailCrm\DeliveryModuleBundle\Integration\Crm\Exception\ApiGatewayException;
use RetailCrm\DeliveryModuleBundle\Integration\Crm\Exception\ForbiddenException;
use RetailCrm\DeliveryModuleBundle\Integration\Crm\Exception\HttpException;
use RetailCrm\DeliveryModuleBundle\Integration\Crm\Exception\LimitException;
use RetailCrm\DeliveryModuleBundle\Model\Account;

class ApiGateway implements ApiGatewayInterface
{
    public static function getCode(): string
    {
        return 'crm';
    }

    private static $defaultOptions = [
        'extra' => [
            'api' => [
                'version' => 'v5',
            ],
        ],
    ];

    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCredentials(Account $account): array
    {
        $response = $this->request($account, 'GET', 'credentials', ['extra' => ['api' => ['version' => false]]]);

        return $response['credentials'] ?? [];
    }

    public function getPaymentTypes(Account $account): array
    {
        $response = $this->request($account, 'GET', 'reference/payment-types');

        return $response['paymentTypes'] ?? [];
    }

    public function getSites(Account $account): array
    {
        $response = $this->request($account, 'GET', 'reference/sites');

        return $response['sites'] ?? [];
    }

    public function getStatuses(Account $account): array
    {
        $response = $this->request($account, 'GET', 'reference/statuses');

        return $response['statuses'] ?? [];
    }

    public function getStores(Account $account): array
    {
        $response = $this->request($account, 'GET', 'reference/stores');

        return $response['stores'] ?? [];
    }

    public function updateIntegrationModule(Account $account, array $integrationModule): void
    {
        if (empty($integrationModule['code'])) {
            throw new \LogicException('Parameter "code" is required');
        }

        $endpoint = sprintf('integration-modules/%s/edit', $integrationModule['code']);

        $options = [
            'form_params' => [
                'integrationModule' => json_encode($integrationModule),
            ],
        ];

        $this->request($account, 'POST', $endpoint, $options);
    }

    public function updateDeliveryStatuses(Account $account, string $integrationCode, array $statuses): void
    {
        if (empty($integrationCode)) {
            throw new \LogicException('Parameter "integrationCode" is required');
        }

        $endpoint = sprintf('delivery/generic/%s/tracking', $integrationCode);

        $options = [
            'form_params' => [
                'statusUpdate' => json_encode($statuses),
            ],
        ];

        $this->request($account, 'POST', $endpoint, $options);
    }

    public function request(Account $account, string $method, string $endpoint, array $options = []): array
    {
        $options = array_merge_recursive(
            static::$defaultOptions,
            [
                'extra' => [
                    'service_code' => static::getCode(),
                    'account_id' => $account->getId(),
                    'client_id' => $account->getClientId(),
                    'debug' => $account->isDebug(),
                ],
            ],
            $options
        );

        switch (strtoupper($method)) {
            case 'GET':
                $options['query']['apiKey'] = $account->getCrmApiKey();

                break;

            case 'POST':
                $options['form_params']['apiKey'] = $account->getCrmApiKey();

                break;
        }

        $uri = $this->buildUri($account, $endpoint, $options);

        try {
            $response = $this->client->request($method, $uri, $options);
        } catch (GuzzleClientException $e) {
            $response = $e->getResponse();

            if (403 === $response->getStatusCode()) {
                throw new ForbiddenException($e->getMessage(), $e);
            }
        } catch (GuzzleServerException $e) {
            $response = $e->getResponse();

            if (503 === $response->getStatusCode()) {
                throw new LimitException($e->getMessage(), $e);
            }
        } catch (GuzzleHttpException $e) {
            throw new HttpException($e->getMessage(), $e);
        }

        $body = json_decode($response->getBody(), true);
        if (!($body['success'] ?? false)) {
            throw new ApiGatewayException(
                $body['errorMsg'] ?? 'An error has occurred while processing your request',
                $body['errors'] ?? []
            );
        }

        return $body;
    }

    private function buildUri(Account $account, string $endpoint, array $options = []): string
    {
        $baseUrl = sprintf('%s/api', $account->getCrmUrl());
        if ($options['extra']['api']['version'] ?? false) {
            $baseUrl .= sprintf('/%s', $options['extra']['api']['version']);
        }

        return sprintf('%s/%s', $baseUrl, $endpoint);
    }
}
