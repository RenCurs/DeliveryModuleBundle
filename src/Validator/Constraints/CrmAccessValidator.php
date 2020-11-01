<?php

namespace RetailCrm\DeliveryModuleBundle\Validator\Constraints;

use RetailCrm\DeliveryModuleBundle\Integration\Crm\ApiGatewayInterface as CrmApiGatewayInterface;
use RetailCrm\DeliveryModuleBundle\Integration\Crm\Exception\ApiGatewayException;
use RetailCrm\DeliveryModuleBundle\Integration\Crm\Exception\ForbiddenException;
use RetailCrm\DeliveryModuleBundle\Integration\Crm\Exception\HttpException;
use RetailCrm\DeliveryModuleBundle\Integration\Crm\Exception\LimitException;
use RetailCrm\DeliveryModuleBundle\Model\Account;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CrmAccessValidator extends ConstraintValidator
{
    /** @var CrmApiGatewayInterface */
    private $crmApiGateway;

    public function __construct(CrmApiGatewayInterface $crmApiGateway)
    {
        $this->crmApiGateway = $crmApiGateway;
    }

    public function validate($account, Constraint $constraint): void
    {
        if (!($account instanceof Account)) {
            throw new UnexpectedTypeException($account, Account::class);
        }

        if (!($constraint instanceof CrmAccess)) {
            throw new UnexpectedTypeException($constraint, CrmAccess::class);
        }

        try {
            $credentials = $this->crmApiGateway->getCredentials($account);

            foreach ($constraint->requiredApiMethods as $method) {
                if (!in_array($method, $credentials)) {
                    $this->context
                        ->buildViolation('retail_crm_access.access_denied', ['%method%' => $method])
                        ->atPath('crmApiKey')
                        ->addViolation()
                    ;
                }
            }
        } catch (ForbiddenException $e) {
            $this->context
                ->buildViolation('crm_access.forbidden_exception')
                ->setCause($e->getMessage())
                ->atPath('crmApiKey')
                ->addViolation()
            ;
        } catch (LimitException $e) {
            $this->context
                ->buildViolation('crm_access.limit_exception')
                ->setCause($e->getMessage())
                ->atPath('crmUrl')
                ->addViolation()
            ;
        } catch (HttpException $e) {
            $this->context
                ->buildViolation('crm_access.http_exception')
                ->setCause($e->getMessage())
                ->atPath('crmUrl')
                ->addViolation()
            ;
        } catch (ApiGatewayException $e) {
            $this->context
                ->buildViolation('crm_access.api_gateway_exception')
                ->setCause($e->getMessage())
                ->atPath('crmUrl')
                ->addViolation()
            ;
        }
    }
}
