<?php

namespace RetailCrm\DeliveryModuleBundle\Model;

class Account
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $crmUrl;

    /** @var string */
    protected $crmApiKey;

    /** @var string */
    protected $clientId;

    /** @var string */
    protected $language;

    /** @var bool */
    protected $active;

    /** @var bool */
    protected $frozen;

    /** @var bool */
    protected $debug;

    /** @var string */
    protected $defaultPayerType;

    /** @var string */
    protected $costCalculateBy;

    /** @var bool */
    protected $nullDeclaredValue;

    /** @var bool */
    protected $lockedByDefault;

    /** @var array */
    protected $deliveryExtraData;

    /** @var array */
    protected $shipmentExtraData;

    /** @var \DateTime */
    protected $trackedAt;

    /** @var \DateTime */
    protected $createdAt;

    public function __construct()
    {
        $this->clientId = uuid_create(UUID_TYPE_RANDOM);
        $this->active = true;
        $this->frozen = false;
        $this->debug = false;
        $this->defaultPayerType = 'sender';
        $this->costCalculateBy = 'auto';
        $this->nullDeclaredValue = false;
        $this->lockedByDefault = false;
        $this->deliveryExtraData = [];
        $this->shipmentExtraData = [];
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCrmUrl(): ?string
    {
        return $this->crmUrl;
    }

    public function setCrmUrl(string $crmUrl): self
    {
        $this->crmUrl = rtrim($crmUrl, '/');

        return $this;
    }

    public function getCrmApiKey(): ?string
    {
        return $this->crmApiKey;
    }

    public function setCrmApiKey(string $crmApiKey): self
    {
        $this->crmApiKey = $crmApiKey;

        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function isFrozen(): bool
    {
        return $this->frozen;
    }

    public function setFrozen(bool $frozen): self
    {
        $this->frozen = $frozen;

        return $this;
    }

    public function isEnabled(): bool
    {
        return !$this->frozen && $this->active;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;

        return $this;
    }

    public function getDefaultPayerType(): string
    {
        return $this->defaultPayerType;
    }

    public function setDefaultPayerType(string $defaultPayerType): self
    {
        $this->defaultPayerType = $defaultPayerType;

        return $this;
    }

    public function getCostCalculateBy(): string
    {
        return $this->costCalculateBy;
    }

    public function setCostCalculateBy(string $costCalculateBy): self
    {
        $this->costCalculateBy = $costCalculateBy;

        return $this;
    }

    public function isNullDeclaredValue(): bool
    {
        return $this->nullDeclaredValue;
    }

    public function setNullDeclaredValue(bool $nullDeclaredValue): self
    {
        $this->nullDeclaredValue = $nullDeclaredValue;

        return $this;
    }

    public function isLockedByDefault(): bool
    {
        return $this->lockedByDefault;
    }

    public function setLockedByDefault(bool $lockedByDefault): self
    {
        $this->lockedByDefault = $lockedByDefault;

        return $this;
    }

    public function getDeliveryExtraData(): array
    {
        return $this->deliveryExtraData;
    }

    public function setDeliveryExtraData(array $deliveryExtraData): self
    {
        $this->deliveryExtraData = $deliveryExtraData;

        return $this;
    }

    public function getShipmentExtraData(): array
    {
        return $this->shipmentExtraData;
    }

    public function setShipmentExtraData(array $shipmentExtraData): self
    {
        $this->shipmentExtraData = $shipmentExtraData;

        return $this;
    }

    public function getTrackedAt(): ?\DateTime
    {
        return $this->trackedAt;
    }

    public function setTrackedAt(\DateTime $trackedAt): self
    {
        $this->trackedAt = $trackedAt;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
