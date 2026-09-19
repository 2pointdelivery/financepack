<?php

namespace FinancePack\Support;

class Column
{
    public function __construct(
        private string $name,
        private string $label,
        private ?string $description = null,
    ) {}

    public static function make(string $name, string $label, ?string $description = null): static
    {
        return new static($name, $label, $description);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
