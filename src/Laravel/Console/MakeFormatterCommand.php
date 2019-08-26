<?php
/**
 * Author: wujiafeng
 * DateTime: 2019-08-19 20:36
 * Email: wujiafeng@linctex.com
 */

namespace Deathel\DataFormatter\Laravel\Console;


use App\Lib\ApiFormatter\Type;
use App\Models\Organize;
use Doctrine\DBAL\Types\BooleanType;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MakeFormatterCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:formatter';

    protected $signature = 'make:formatter {name} {model?} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new formatter class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Formatter';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/formatter.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Formatter';
    }

    /**
     * @var null | Model
     */
    protected $model = null;

    public function handle()
    {
        $modelClass = $this->argument('model');
        if (class_exists($modelClass)) {
            $model = new $modelClass();
            if ($model instanceof Model) {
                $this->model = $model;
            }
        }

        return parent::handle();
    }

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        return $this->replaceNamespace($stub, $name)->replaceFields($stub)->replaceClass($stub, $name);
    }

    protected function replaceFields(&$stub)
    {
        if (!$this->model) {
            $stub = str_replace('DummyField', '', $stub);
            return $this;
        }

        $columns = $this->getColumns();

        $realFields = '';
        $index      = 0;
        foreach ($columns as $key => $column) {
            $name       = $column->getName();
            $type       = $column->getType()->getName();
            $tab        = ($index != 0) ? '        ' : '';
            $realFields .= $tab . "\"$name\"" . ' => ' . $this->getFormatterTypeString($type) . ",\n";
            $index++;
        }

        $stub = str_replace('DummyField', $realFields, $stub);
        return $this;
    }

    protected function getFormatterTypeString($type)
    {
        switch ($type) {
            case "integer":
                $res = "Type::INT";
                break;
            case "string":
            case "text":
                $res = "Type::STRING";
                break;
            case "boolean":
                $res = "Type::BOOL";
                break;
            case "float":
                $res = "Type::FLOAT";
                break;
            default:
                $res = "Type::ORIGIN";
        }
        return $res;
    }


    protected function getColumns()
    {
        $connection = $this->model->getConnection();

        // compatible with bit type
        $connection->getDoctrineConnection()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('bit', 'boolean');

        $table = $this->model->getTable();
        $table = $connection->getTablePrefix() . $table;

        return $connection->getDoctrineSchemaManager()
            ->listTableDetails($table)->getColumns();
    }
}