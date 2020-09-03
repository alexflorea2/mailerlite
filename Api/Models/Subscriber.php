<?php

namespace Api\Models;

class Subscriber implements \JsonSerializable
{
    protected ?float $id = null;
    protected string $email;
    protected string $name;
    protected string $state;
    protected ?string $source = null;
    protected array $fields = [];

    public const STATE_UNCONFIRMED = 'unconfirmed';
    public const STATE_ACTIVE = 'active';
    public const STATE_UNSUBSCRIBED = 'unsubscribed';
    public const STATE_JUNK = 'junk';

    public const ALLOWED_STATES = [
        self::STATE_UNCONFIRMED,
        self::STATE_ACTIVE,
        self::STATE_UNSUBSCRIBED,
        self::STATE_JUNK,
    ];

    public function __construct(
        string $email,
        string $name,
        string $state = self::STATE_UNCONFIRMED,
        ?string $source = null
    ) {
        $this->setEmail($email)
            ->setName($name)
            ->setState($state)
            ->setSource($source);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $value): self
    {
        if (!in_array($value, self::ALLOWED_STATES)) {
            throw new \Exception("State {$value} not defined for resource.");
        }

        $this->state = $value;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $value): self
    {
        $this->source = $value;

        return $this;
    }

    public function setEmail(string $value): self
    {
        if( filter_var($value, FILTER_VALIDATE_EMAIL) === false)
        {
            throw new \Exception("Value {$value} is not a valid email address format.");
        }

        $this->email = $value;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $value): self
    {
        $this->name = $value;

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

    public function getFields(): array
    {
        return $this->fields;
    }

    public function addField(SubscriberField $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'name' => $this->getName(),
            'state' => $this->getState(),
            'source' => $this->getSource()
        ];
    }
}
