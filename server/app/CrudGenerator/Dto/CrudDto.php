<?php

namespace App\CrudGenerator\Dto;

use Illuminate\Support\Collection;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

/**
 * Used to transfer data properly between CRUD services and interfaces.
 * This data transfer object contain instructions how CRUD backend should be created.
 */
class CrudDto
{
    private string $model_name;

    private string $friendly_name;

    private string $friendly_name_plural;

    private string $name_column;

    private Collection $attributes;

    public function getModelNameSnake(): string
    {
        return Str::snake($this->model_name);
    }

    public function getModelNameSnakePlural(): string
    {
        return Pluralizer::plural(Str::snake($this->model_name));
    }

    public function getModelNameStudly(): string
    {
        return Str::studly($this->model_name);
    }

    public function getModelName(): string
    {
        return $this->model_name;
    }

    public function setModelName(string $model_name): void
    {
        $this->model_name = $model_name;
    }

    public function getFriendlyName(): string
    {
        return Str::lower($this->friendly_name);
    }

    public function setFriendlyName(string $friendly_name): void
    {
        $this->friendly_name = $friendly_name;
    }

    public function getFriendlyNamePlural(): string
    {
        return Str::lower($this->friendly_name_plural);
    }

    public function getFriendlyNamePluralUcfirst(): string
    {
        return Str::ucfirst($this->friendly_name_plural);
    }

    public function setFriendlyNamePlural(string $friendly_name_plural): void
    {
        $this->friendly_name_plural = $friendly_name_plural;
    }

    public function getNameColumn(): string
    {
        return $this->name_column;
    }

    public function setNameColumn(string $name_column): void
    {
        $this->name_column = $name_column;
    }

    public function setAttributes(Collection $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
