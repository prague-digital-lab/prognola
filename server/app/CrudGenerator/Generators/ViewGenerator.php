<?php

namespace App\CrudGenerator\Generators;

use App\CrudGenerator\Dto\Attribute;
use App\CrudGenerator\Dto\CrudDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionException;

class ViewGenerator
{
    private string $model_name_snake;

    private string $model_name_snake_plural;

    private string $model_name_camel;

    private string $friendly_name;

    private string $friendly_name_plural;

    private string $friendly_name_plural_ucfirst;

    private string $name_column;

    private CrudDto $dto;

    public function __construct(CrudDto $dto)
    {
        $this->dto = $dto;

        $this->model_name_snake = $dto->getModelNameSnake();
        $this->model_name_snake_plural = $dto->getModelNameSnakePlural();
        $this->model_name_camel = $dto->getModelNameStudly();

        $this->friendly_name = $dto->getFriendlyName();
        $this->friendly_name_plural = $dto->getFriendlyNamePlural();
        $this->friendly_name_plural_ucfirst = Str::ucfirst($dto->getFriendlyNamePluralUcfirst());

        $this->name_column = $dto->getNameColumn();
    }

    public function getIndex(): string
    {
        return "@extends('admin::layout')
@section('content')
    <div class='row mb-4'>
        <div class='col-md-12'>
            <h2>$this->friendly_name_plural_ucfirst</h2>

            <a href='{{ route('admin.$this->model_name_snake_plural.create') }}'
           class='btn btn-success'>Nový $this->friendly_name</a>
        </div>
    </div>

    <table class='table'>
        <thead>
        <tr>
            <th>Název</th>
        </tr>
        </thead>
        <tbody>

        @foreach(\$$this->model_name_snake_plural as \$$this->model_name_snake)
            <tr>
                <td><a href='{{ route('admin.$this->model_name_snake_plural.edit', \$$this->model_name_snake) }}'>{{\$$this->model_name_snake->$this->name_column}}</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
        ";

    }

    public function getCreate()
    {
        $form_inputs = $this->getAttributeInputFieldsForCreate($this->dto->getAttributes());

        return "@extends('admin::layout')

@section('content')
    <h2>Nový $this->friendly_name</h2>

    <hr>

    <form action='{{route('admin.$this->model_name_snake_plural.store')}}'
          method='post'
          enctype='multipart/form-data'>
          @csrf
    <div class='row'>
        <div class='col-md-6'>

                $form_inputs


        </div>

        <div class='col-md-6'>
            <div class='card'>
                <div class='card-header'>Možnosti nového záznamu</div>
                <div class='card-body'>
                    <input type='submit'
                           value='Vytvořit'
                           class='btn btn-success'>

                    <a href='{{route('admin.$this->model_name_snake_plural.index')}}'
                       class='btn btn-outline-secondary'
                       onclick='return confirm(\"Opravdu chcete odejít? Veškerá zadaná data budou ztracena.\")'>Storno</a>

                </div>
            </div>
        </div>
    </div>
    </form>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.multiselect').multiselect();
        });
    </script>
@endpush
        ";
    }

    public function getEdit()
    {
        $form_inputs = $this->getAttributeInputFieldsForEdit($this->dto->getAttributes());

        return "@extends('admin::layout')

@section('content')
    <h2>{{\$$this->model_name_snake->$this->name_column}}</h2>
    <p>$this->friendly_name</p>

    <hr>

    <form action='{{route('admin.$this->model_name_snake_plural.update', \$$this->model_name_snake)}}'
          method='post'
          enctype='multipart/form-data'>
          @csrf
          @method('PATCH')
        <div class='row'>
            <div class='col-md-6'>

                $form_inputs

            </div>
            <div class='col-md-6'>
                <div class='card'>
                    <div class='card-header'>Možnosti úprav</div>
                    <div class='card-body'>
                        @if(method_exists(\$$this->model_name_snake, 'getPublicUrl'))
                            Veřejný odkaz: <a target='_blank' href='{{ \$$this->model_name_snake->getPublicUrl() }}'>{{ \$$this->model_name_snake->getPublicUrl() }}</a>
                            <hr>
                        @endif

                        <input type='submit'
                           value='Uložit změny'
                           class='btn btn-success'>

                        <a href='{{route('admin.$this->model_name_snake_plural.index')}}'
                           class='btn btn-outline-secondary'
                           onclick='return confirm(\"Opravdu chcete odejít? Veškeré změny budou ztraceny.\")'>Storno</a>

                        <hr>

                        <a class='text-danger' href='#' onclick='deleteRecord()'>Odstranit</a>
                    </div>

                    <script>
                        function deleteRecord() {
                            if(confirm(\"Opravdu chcete tento záznam odstranit? Tato akce je nevratná.\"))
                            {
                                document.getElementById(\"delete-record\").submit();
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </form>

    <form id='delete-record' method='post' action='{{route(\"admin.$this->model_name_snake_plural.destroy\", \$$this->model_name_snake)}}'>
        @csrf
        @method('DELETE')

    </form>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.multiselect').multiselect();
        });
    </script>
@endpush
        ";
    }

    private function getAttributeInputFieldsForCreate(Collection $attributes): string
    {
        $input_content = '';

        /**
         * @var Attribute $attribute
         */
        foreach ($attributes as $attribute) {
            $type = $attribute->type;

            // String
            if ($type == Attribute::TYPE_STRING) {
                $input_content .= $this->getCreateStringAttribute($attribute);
            }
            // Text
            elseif ($type == Attribute::TYPE_TEXT) {
                $input_content .= $this->getCreateTextAttribute($attribute);
            }
            // Datetime
            elseif ($type == Attribute::TYPE_DATE_TIME) {
                $input_content .= $this->getCreateDateTimeAttribute($attribute);
            }
            // Datetime
            elseif ($type == Attribute::TYPE_BOOLEAN) {
                $input_content .= $this->getCreateBooleanAttribute($attribute);
            } elseif ($type == Attribute::TYPE_EMAIL) {
                $input_content .= $this->getCreateEmailAttribute($attribute);
            } elseif ($type == Attribute::TYPE_URL_ADDRESS) {
                $input_content .= $this->getCreateUrlAttribute($attribute);
            } elseif ($type == Attribute::TYPE_RELATION_BELONGS_TO) {
                $input_content .= $this->getCreateBelongsToAttribute($attribute);
            } elseif ($type == Attribute::TYPE_RELATION_BELONGS_TO_MANY) {
                $input_content .= $this->getCreateBelongsToManyAttribute($attribute);
            } elseif ($type == Attribute::TYPE_RELATION_PHOTO_MODEL) {
                $input_content .= $this->getCreatePhotoAttribute($attribute);
            }
        }

        return $input_content;
    }

    private function getAttributeInputFieldsForEdit(Collection $attributes): string
    {
        $input_content = '';

        /**
         * @var Attribute $attribute
         */
        foreach ($attributes as $attribute) {
            $type = $attribute->type;

            // String
            if ($type == Attribute::TYPE_STRING) {
                $input_content .= $this->getEditStringAttribute($attribute);
            }
            // Text
            elseif ($type == Attribute::TYPE_TEXT) {
                $input_content .= $this->getEditTextAttribute($attribute);
            }
            // Datetime
            elseif ($type == Attribute::TYPE_DATE_TIME) {
                $input_content .= $this->getEditDateTimeAttribute($attribute);
            }
            // Boolean
            elseif ($type == Attribute::TYPE_BOOLEAN) {
                $input_content .= $this->getEditBooleanAttribute($attribute);
            }
            // Email
            elseif ($type == Attribute::TYPE_EMAIL) {
                $input_content .= $this->getEditEmailAttribute($attribute);
            }
            // URL
            elseif ($type == Attribute::TYPE_URL_ADDRESS) {
                $input_content .= $this->getEditUrlAttribute($attribute);
            } elseif ($type == Attribute::TYPE_RELATION_BELONGS_TO) {
                $input_content .= $this->getEditBelongsToAttribute($attribute);
            } elseif ($type == Attribute::TYPE_RELATION_BELONGS_TO_MANY) {
                $input_content .= $this->getEditBelongsToManyAttribute($attribute);
            } elseif ($type == Attribute::TYPE_RELATION_PHOTO_MODEL) {
                $input_content .= $this->getEditPhotoAttribute($attribute);
            }
        }

        return $input_content;
    }

    private function getCreateStringAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <input id='$attribute->column_name'
                           type='text'
                           class='form-control'
                           name='$attribute->column_name' $required>
                </div>

        ";
    }

    private function getCreateDateTimeAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <input id='$attribute->column_name'
                           type='datetime-local'
                           class='form-control'
                           name='$attribute->column_name' $required>
                </div>

        ";
    }

    private function getCreateBooleanAttribute(Attribute $attribute): string
    {
        return "
                <div class='form-check mb-4'>
                    <input id='$attribute->column_name'
                            class='form-check-input'
                           type='checkbox'
                           class='form-control'
                           name='$attribute->column_name'>
                    <label class='form-check-label' for='$attribute->column_name'>$attribute->friendly_name</label>
                </div>
        ";
    }

    private function getCreateTextAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <textarea id='$attribute->column_name'
                           class='form-control'
                           rows='5'
                           name='$attribute->column_name'
                           $required></textarea>
                    <small>Pouze čistý text bez formátování. Je možné zalamovat řádky.</small>
                </div>

        ";
    }

    private function getCreateBelongsToAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';
        $related_model_name_studly = $attribute->relatedModelNameStudly();
        $related_model_name_snake = $attribute->relatedModelNameSnake();

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>

                    <select name='$attribute->column_name'
                            id='$attribute->column_name'
                            class='form-control'
                            $required>
                        @foreach(App\\Models\\$related_model_name_studly::all() as \$$related_model_name_snake)
                        <option value='{{\$".$related_model_name_snake."->id}}'>{{\$".$related_model_name_snake.'->name}}</option>
                        @endforeach
                    </select>

                </div>

        ';
    }

    private function getEditStringAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <input id='$attribute->column_name'
                           type='text'
                           class='form-control'
                           value='{{ \$$this->model_name_snake->$attribute->column_name }}'
                           name='$attribute->column_name' $required>
                </div>

        ";
    }

    private function getEditDateTimeAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <input id='$attribute->column_name'
                           type='datetime-local'
                           class='form-control'
                           value='{{ \$$this->model_name_snake->$attribute->column_name->format('Y-m-d\TH:i') }}'
                           name='$attribute->column_name' $required>
                </div>

        ";
    }

    private function getEditBooleanAttribute(Attribute $attribute): string
    {
        return "
                <div class='form-check mb-4'>
                        <input id='$attribute->column_name'
                           type='checkbox'
                           class='form-check-input'
                           {{ \$$this->model_name_snake->$attribute->column_name ? 'checked' : '' }}
                           name='$attribute->column_name'>
                        <label class='form-check-label' for='$attribute->column_name'>$attribute->friendly_name</label>
                </div>
        ";
    }

    private function getEditTextAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <textarea id='$attribute->column_name'
                           class='form-control'
                           rows='5'
                           name='$attribute->column_name'
                           $required>{{ \$$this->model_name_snake->$attribute->column_name }}</textarea>
                    <small>Pouze čistý text bez formátování. Je možné zalamovat řádky.</small>
                </div>

        ";
    }

    private function getEditBelongsToAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';
        $related_model_name_studly = $attribute->relatedModelNameStudly();
        $related_model_name_snake = $attribute->relatedModelNameSnake();

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>

                    <select name='$attribute->column_name'
                            id='$attribute->column_name'
                            class='form-control'
                            $required>
                        @foreach(App\\Models\\$related_model_name_studly::all() as \$$related_model_name_snake)
                        <option value='{{\$".$related_model_name_snake."->id}}'
                            {{ \$$this->model_name_snake->$attribute->column_name ==  \$".$related_model_name_snake."->id ? 'selected' : '' }}
                            >{{\$".$related_model_name_snake.'->name}}</option>
                        @endforeach
                    </select>

                </div>

        ';
    }

    private function getCreateEmailAttribute(Attribute $attribute)
    {
        $required = $attribute->required ? 'required' : '';

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <input id='$attribute->column_name'
                           type='email'
                           class='form-control'
                           name='$attribute->column_name' $required>
                </div>

        ";
    }

    private function getEditEmailAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <input id='$attribute->column_name'
                           type='email'
                           class='form-control'
                           value='{{ \$$this->model_name_snake->$attribute->column_name }}'
                           name='$attribute->column_name' $required>
                </div>

        ";
    }

    private function getCreateUrlAttribute(Attribute $attribute)
    {
        $required = $attribute->required ? 'required' : '';

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <input id='$attribute->column_name'
                           type='url'
                           class='form-control'
                           name='$attribute->column_name' $required>
                </div>

        ";
    }

    private function getEditUrlAttribute(Attribute $attribute)
    {
        $required = $attribute->required ? 'required' : '';

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <input id='$attribute->column_name'
                           type='url'
                           class='form-control'
                           value='{{ \$$this->model_name_snake->$attribute->column_name }}'
                           name='$attribute->column_name' $required>
                </div>

        ";
    }

    private function getEditBelongsToManyAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';
        $related_model = $attribute->getRelatedModel();
        $related_model_name_snake = $attribute->relatedModelNameSnake();

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>

                    <br>

                    <select name='$attribute->column_name[]'
                            id='$attribute->column_name'
                            class='multiselect'
                            multiple
                            $required>
                        @foreach($related_model::all() as \$$related_model_name_snake)
                        <option value='{{\$".$related_model_name_snake."->id}}'
                            {{ \$$this->model_name_snake->$attribute->column_name->find(\$".$related_model_name_snake.") ? 'selected' : '' }}
                            >{{\$".$related_model_name_snake.'->name}}</option>
                        @endforeach
                    </select>

                </div>

        ';
    }

    private function getCreateBelongsToManyAttribute(Attribute $attribute)
    {
        $required = $attribute->required ? 'required' : '';
        $related_model = $attribute->getRelatedModel();
        $related_model_name_snake = $attribute->relatedModelNameSnake();

        return "
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>

                    <br>

                    <select name='$attribute->column_name'
                            id='$attribute->column_name'
                            class='multiselect'
                            multiple
                            $required>
                        @foreach($related_model::all() as \$$related_model_name_snake)
                        <option value='{{\$".$related_model_name_snake."->id}}'
                            >{{\$".$related_model_name_snake.'->name}}</option>
                        @endforeach
                    </select>

                </div>

        ';
    }

    /**
     * @throws ReflectionException
     */
    private function getEditPhotoAttribute(Attribute $attribute): string
    {
        $required = $attribute->required ? 'required' : '';
        $related_photo_model_name = $attribute->relatedModelNameSnake();

        return <<<PHP
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <br>
                    <input id='$attribute->column_name'
                           type='file'
                           accept='image/jpeg,image/png'
                           name='$attribute->column_name' $required>
                </div>

                @if(\$$this->model_name_snake->$attribute->column_name)
                    <img src='{{\$$this->model_name_snake->{$related_photo_model_name}->url()}}'
                    width='60%'
                    class='img-thumbnail'>
                @endif

PHP;
    }

    private function getCreatePhotoAttribute(Attribute $attribute)
    {
        $required = $attribute->required ? 'required' : '';

        return <<<PHP
                <div class='form-group'>
                    <label for='$attribute->column_name'>$attribute->friendly_name</label>
                    <br>
                    <input id='$attribute->column_name'
                           type='file'
                           accept='image/jpeg,image/png'
                           name='$attribute->column_name' $required>
                </div>

PHP;
    }
}
