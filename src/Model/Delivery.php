<?php

namespace RetailCrm\DeliveryModuleBundle\Model;

class Delivery
{
    /** @var int */
    protected $id;

    /** @var Account */
    protected $account;

    /** @var int */
    protected $orderId;

    /** @var string */
    protected $externalId;

    /** @var string */
    protected $trackNumber;

    /** @var bool */
    protected $ended;

    /** @var \DateTime */
    protected $createdAt;

    public function __construct(Account $account)
    {
        $this->account = $account;
        $this->ended = false;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getTrackNumber(): ?string
    {
        return $this->trackNumber;
    }

    public function setTrackNumber(string $trackNumber): self
    {
        $this->trackNumber = $trackNumber;

        return $this;
    }

    public function isEnded(): bool
    {
        return $this->ended;
    }

    public function setEnded(bool $ended): self
    {
        $this->ended = $ended;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
