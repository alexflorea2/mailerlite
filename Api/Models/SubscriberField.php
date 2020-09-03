<?php

namespace Api\Models;

class SubscriberField implements \JsonSerializable
{
    protected ?float $id = null;
    protected ?float $fieldId = null;
    protected ?float $subscriberId = null;
    protected string $value;
    protected ?Field $field = null;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getFields(): ?Field
    {
        return $this->field;
    }

    public function setField(Field $value): self
    {
        $this->field = $value;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value)
    {
        $this->value = $value;

        return $this;
    }

    public function getSubscriberId(): ?int
    {
        return $this->subscriberId;
    }

    public function setSubscriberId(int $value): self
    {
        $this->subscriberId = $value;

        return $this;
    }

    public function getFieldId(): ?int
    {
        return $this->fieldId;
    }

    public function setFieldId(int $value): self
    {
        $this->fieldId = $value;

        return $this;
    }

    public function getId(): ?float
    {
        return $this->id;
    }

    public function setId(float $value): self
    {
        if (isset($this->id)) {
            throw new \Exception('Cannot modify id field if already set from DB');
        }

        $this->id = $value;

        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'field_id' => $this->getFieldId(),
            'subscriber_id' => $this->getSubscriberId(),
            'value' => $this->getValue()
        ];
    }
}
