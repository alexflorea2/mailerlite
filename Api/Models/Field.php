<?php

namespace Api\Models;

class Field implements \JsonSerializable
{
    protected ?float $id = null;
    protected string $title;
    protected string $type;
    protected ?string $description = null;
    protected ?string $default_value = null;

    public const TYPE_DATE = 'date';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_STRING = 'string';
    public const TYPE_NUMBER = 'number';

    public const ALLOWED_TYPES = [
        self::TYPE_DATE,
        self::TYPE_BOOLEAN,
        self::TYPE_STRING,
        self::TYPE_NUMBER,
    ];

    public function __construct(string $title, string $type, ?string $description = null, ?string $default_value = null)
    {
        $this->setTitle($title)
            ->setType($type)
            ->setDescription($description)
            ->setDefaultValue($default_value);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $value): self
    {
        $this->title = $value;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $value): self
    {
        if (!in_array($value, self::ALLOWED_TYPES)) {
            throw new \Exception("Type {$value} not defined for resource.");
        }

        $this->type = $value;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $value): self
    {
        $this->description = $value;

        return $this;
    }

    public function getDefaultValue(): ?string
    {
        return $this->default_value;
    }

    public function setDefaultValue(?string $value): self
    {
        $this->default_value = $value;

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
            'title' => $this->getTitle(),
            'type' => $this->getType(),
            'description' => $this->getDescription(),
            'default_value' => $this->getDefaultValue()
        ];
    }
}
