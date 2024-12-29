<?php

namespace App\CrudGenerator;

use App\CrudGenerator\Dto\CrudDto;
use App\CrudGenerator\Generators\ControllerGenerator;
use Illuminate\Filesystem\Filesystem;

class CrudControllerService
{
    private CrudDto $dto;

    private Filesystem $files;

    public function __construct(Filesystem $filesystem, CrudDto $dto)
    {
        $this->dto = $dto;
        $this->files = $filesystem;
    }

    public function createCrudController(): bool
    {
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $crudControllerGenerator = new ControllerGenerator($this->dto);

        $contents = $crudControllerGenerator->makeResourceControllerFileContent();

        // Save controller
        $this->files->put($path, $contents);

        return true;
    }

    /**
     * Get the full path of generate class
     */
    private function getSourceFilePath(): string
    {
        return base_path('App/Http/Controllers/Admin').'/'.$this->dto->getModelNameStudly().'Controller.php';
    }

    /**
     * Build the directory for the class if necessary.
     */
    protected function makeDirectory(string $path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
