<?php

namespace App\CrudGenerator\Generators;

use App\CrudGenerator\Dto\Attribute;
use App\CrudGenerator\Dto\CrudDto;

class ControllerGenerator
{
    private string $namespace;

    private string $controller_name;

    private string $model_name_snake;

    private string $model_name_studly;

    private string $model_name_snake_plural;

    private CrudDto $dto;

    public function __construct(CrudDto $dto)
    {
        $this->dto = $dto;

        $this->model_name_snake = $this->dto->getModelNameSnake();
        $this->model_name_snake_plural = $this->dto->getModelNameSnakePlural();
        $this->model_name_studly = $this->dto->getModelNameStudly();

        $this->namespace = 'App\\Http\\Controllers\\Admin';
        $this->controller_name = $this->model_name_studly.'Controller';
    }

    public function makeResourceControllerFileContent()
    {
        return $this->getTemplate();
    }

    private function getTemplate()
    {
        $modelFieldSetters = $this->getModelFieldSetters();

        return "<?php

namespace $this->namespace;

use App\Models\\$this->model_name_studly;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class $this->controller_name
 *
 * This admin CRUD controller was generated automatically.
 */
class $this->controller_name extends Controller
{
    /**
     * Display a listing of the $this->model_name_snake_plural.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin.$this->model_name_snake_plural.index', [
            '$this->model_name_snake_plural' => $this->model_name_studly::all()
        ]);
    }

    /**
     * Show the form for creating a new $this->model_name_snake.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
         return view('admin.$this->model_name_snake_plural.create');
    }

    /**
     * Store a newly created $this->model_name_snake in storage.
     *
     * @param  Request \$request
     *
     * @return RedirectResponse
     */
    public function store(Request \$request)
    {
        \$$this->model_name_snake = new $this->model_name_studly;
        \$$this->model_name_snake->fill(\$request->toArray());
        $modelFieldSetters

        \$$this->model_name_snake->save();

        return redirect()->route('admin.$this->model_name_snake_plural.edit', \$$this->model_name_snake)
                         ->with('alert', 'Záznam byl úspěšně vytvořen.');
    }

//    /**
//     * Display the specified $this->model_name_snake.
//     *
//     * @param $this->model_name_studly \$$this->model_name_snake
//     *
//     * @return Application|Factory|View
//     */
//    public function show($this->model_name_studly \$$this->model_name_snake)
//    {
//         return view('admin.$this->model_name_snake_plural.show', [
//             '$this->model_name_snake' => \$$this->model_name_snake
//         ]);
//    }

    /**
     * Show the form for editing the specified $this->model_name_snake.
     *
     * @param $this->model_name_studly \$$this->model_name_snake
     *
     * @return Application|Factory|View
     */
    public function edit($this->model_name_studly \$$this->model_name_snake)
    {
        return view('admin.$this->model_name_snake_plural.edit', [
            '$this->model_name_snake' => \$$this->model_name_snake
        ]);
    }

    /**
     * Update the specified $this->model_name_snake in storage.
     *
     * @return RedirectResponse
     */
    public function update(Request \$request, $this->model_name_studly \$$this->model_name_snake)
    {
         \$$this->model_name_snake->fill(\$request->toArray());
         $modelFieldSetters

         \$$this->model_name_snake->save();

         return redirect()->route('admin.$this->model_name_snake_plural.index')
                          ->with('alert', 'Záznam byl úspěšně uložen.');
    }

    /**
     * Remove the specified $this->model_name_snake  from storage.
     *
     * @param $this->model_name_studly \$$this->model_name_snake
     *
     * @return RedirectResponse
     */
    public function destroy($this->model_name_studly \$$this->model_name_snake)
    {
        \$$this->model_name_snake->delete();

        return redirect()->route('admin.$this->model_name_snake_plural.index')
                         ->with('alert', 'Záznam byl úspěšně odstraněn.');
    }
}



";

    }

    private function getModelFieldSetters(): string
    {
        $content = '';

        /** @var Attribute $attribute */
        foreach ($this->dto->getAttributes() as $attribute) {
            if ($attribute->type == Attribute::TYPE_BOOLEAN) {
                $content .= "
        \$$this->model_name_snake->$attribute->column_name = \$request->has('$attribute->column_name');";
            }

            if ($attribute->type == Attribute::TYPE_RELATION_BELONGS_TO_MANY) {
                $content
                    .= <<<PHP


        // Sync $this->model_name_snake ids (belongs to many relation)
        \$records = \$request->input('$attribute->column_name');
        $$this->model_name_snake->$attribute->column_name()->sync(\$records);
PHP;

            }

            if ($attribute->type == Attribute::TYPE_RELATION_PHOTO_MODEL) {
                $content
                    .= <<<PHP


        if (\$request->hasFile('$attribute->column_name'))
        {
            // Save photo
            \$ps = new \MichaelDojcar\LaravelPhoto\PhotoService();
            \$photo = \$ps->create('', \$request->file('$attribute->column_name'));
            \$event->$attribute->column_name = \$photo->id;
        }
PHP;
            }
        }

        return $content;
    }
}
