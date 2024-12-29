<?php

namespace App\CrudGenerator;

use App\CrudGenerator\Dto\CrudDto;
use Exception;
use Illuminate\Filesystem\Filesystem;

class CrudService
{
    private CrudViewService $crudViewService;

    private CrudControllerService $crudControllerService;

    private Filesystem $files;

    private ?CrudDto $dto;

    public function __construct(Filesystem $filesystem)
    {
        $this->files = $filesystem;
    }

    public function loadData(CrudDto $dto): void
    {
        $this->dto = $dto;
        $this->crudViewService = new CrudViewService($this->files, $dto);
        $this->crudControllerService = new CrudControllerService($this->files, $dto);
    }

    /**
     * Generate and store admin CRUD views, controller and route signature.
     *
     * @throws Exception
     */
    public function makeWholeCrud(): void
    {
        if (! $this->dto) {
            throw new Exception('There is no dto object with crud instructions.');
        }

        $this->crudViewService->createAllCrudViews();
        $this->crudControllerService->createCrudController();
    }

    /**
     * @throws Exception
     */
    public function validateModelBeforeCrudGenerating($model_name): bool
    {
        $model_name = \Illuminate\Support\Str::studly($model_name);
        $model_type = "App\Models\\$model_name";
        $model = new $model_type;

        return true;
    }
}
