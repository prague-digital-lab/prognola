<?php

namespace App\CrudGenerator\Dto;

use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;

class Attribute
{
    const TYPE_STRING = 'string';

    const TYPE_TEXT = 'text';

    const TYPE_EMAIL = 'email';

    const TYPE_URL_ADDRESS = 'url';

    const TYPE_DATE_TIME = 'datetime';

    const TYPE_BOOLEAN = 'boolean';

    const TYPE_RELATION_BELONGS_TO = 'belongs_to';

    const TYPE_RELATION_BELONGS_TO_MANY = 'belongs_to_many';

    const TYPE_RELATION_PHOTO_MODEL = 'photo';

    public string $model_name;

    public string $column_name;

    public string $related_model;

    public string $friendly_name;

    public string $type;

    public bool $required;

    /**
     * @throws ReflectionException
     */
    public function relatedModelNameStudly(): string
    {
        $reflection = new ReflectionClass($this->getRelatedModel());
        $short_name = $reflection->getShortName();

        return Str::studly($short_name);
    }

    /**
     * @throws ReflectionException
     */
    public function relatedModelNameSnake(): string
    {
        $reflection = new ReflectionClass($this->getRelatedModel());
        $short_name = $reflection->getShortName();

        return Str::snake($short_name);
    }

    public function getRelatedModel(): string
    {
        return $this->related_model;
    }

    public function setRelatedModel(string $related_model): void
    {
        $this->related_model = $related_model;
    }
}
