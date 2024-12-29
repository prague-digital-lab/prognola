<?php

namespace App\CrudGenerator;

use App\CrudGenerator\Dto\CrudDto;
use App\CrudGenerator\Generators\ViewGenerator;
use Illuminate\Filesystem\Filesystem;

class CrudViewService
{
    public function __construct(Filesystem $filesystem, CrudDto $dto)
    {
        $this->files = $filesystem;
        $this->dto = $dto;
        $this->generator = new ViewGenerator($dto);
    }

    public function createAllCrudViews(): void
    {
        $this->createIndexView();
        $this->createCreateView();
        $this->createEditView();
    }

    private function createIndexView(): void
    {
        // Prepare data
        $index_path = $this->getSourceFilePath('index');
        $contents_index = $this->generator->getIndex();

        // Make directory
        $this->makeDirectory(dirname($index_path));

        // Generate view
        $this->generateView($index_path, $contents_index);
    }

    private function createCreateView(): void
    {
        // Prepare data
        $create_path = $this->getSourceFilePath('create');
        $contents_create = $this->generator->getCreate();

        // Make directory
        $this->makeDirectory(dirname($create_path));

        // Generate view
        $this->generateView($create_path, $contents_create);
    }

    private function createEditView(): void
    {
        // Prepare data
        $edit_path = $this->getSourceFilePath('edit');
        $contents_edit = $this->generator->getEdit();

        // Make directory
        $this->makeDirectory(dirname($edit_path));

        // Generate view
        $this->generateView($edit_path, $contents_edit);
    }

    /**
     * Get the full path of generate class
     */
    private function getSourceFilePath(string $view_name): string
    {
        return base_path('resources/views/admin').'/'.$this->dto->getModelNameSnakePlural().'/'.$view_name.'.blade.php';
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

    private function generateView(string $path, $content): void
    {
        $this->files->put($path, $content);
    }
}
