<?php

namespace Havennow\LaravelModule\Console\Commands;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\NoopWordInflector;
use Havennow\LaravelModule\Contracts\ModuleAbstract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * @see \Havennow\LaravelModule\Module
 */
class MakeModule extends Command
{

    const SUB_FOLDERS = ['Controllers', 'Models', 'Views', 'Composers'];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-module:make-module {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create module files';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = (string)$this->argument('name');
        $this->makeModuleFolder();
        $this->makeModuleNameFolder($name);
        $this->makeDefaultModuleFile($name);
        $this->makeDefaultModuleComposerFile($name);
        $this->makeDefaultModuleView($name);
        $this->makeDefaultModuleControllerFile($name);

        $this->info("Done!");
    }

    private function getPathOfModules(): ?string
    {
        $config = config('modules');

        return $config['path'] ?? null;
    }

    private function getNamespaceOfModules(): ?string
    {
        $config = config('modules');

        return $config['namespace'] ?? null;
    }

    private function makeModuleNameFolder($name)
    {

        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
        $path = sprintf('%s/%s', $this->getPathOfModules(), $inflector->classify($name));
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());

        if (!is_dir($path)) {

            File::makeDirectory($path);
            $this->info('Created module path : ' . $path);
        }

        $this->makeSubfolders($path);

    }

    private function makeSubfolders($path)
    {
        if (is_dir($path)) {
            foreach (self::SUB_FOLDERS as $subFolder) {
                $subFolderPath = sprintf('%s/%s', $path, $subFolder);

                if (is_dir($subFolderPath)) {
                    continue;
                }

                File::makeDirectory($subFolderPath);
                $this->info('Created module path : ' . $subFolderPath);
            }
        }
    }

    private function makeDefaultModuleComposerFile($name)
    {
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
        $path = sprintf('%s/%s', $this->getPathOfModules(), $inflector->classify($name));
        $pathComposer = sprintf('%s/%s', $path, self::SUB_FOLDERS[3]);
        $nameSpace = sprintf('%s\%s\%s', $this->getNamespaceOfModules(), $name, self::SUB_FOLDERS[3]);

        //Module root

        $code = <<<PHP
<?php

namespace #namespace#;

use Illuminate\View\View;

class #name#Composer
{
    /**
     * Bind data to the view.
     *
     * @param  View  #view#
     * @return void
     */
    public function compose(View #view#)
    {
        #view#->with([
            'filename' => #view#->getPath()
        ]);
    }
}

PHP;


        $code = str_replace(
            ['#view#', '#namespace#', '#name#'],
            ['$view', $nameSpace, $name],
            $code
        );

        file_put_contents(sprintf('%s/%sComposer.php', $pathComposer, $name), $code);

    }

    private function makeDefaultModuleControllerFile($name)
    {
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
        $path = sprintf('%s/%s', $this->getPathOfModules(), $inflector->classify($name));
        $pathController = sprintf('%s/%s', $path, self::SUB_FOLDERS[0]);
        $nameSpace = sprintf('%s\%s\%s', $this->getNamespaceOfModules(), $name, self::SUB_FOLDERS[0]);
        $nameLowerCase = ltrim(strtolower(preg_replace('/[A-Z]/', '-$0', $name )), '-');

        //Module root

        $code = <<<PHP
<?php

namespace #namespace#;

use App\Http\Controllers\Controller;

class #name#Controller extends Controller
{
    public function index()
    {
        return view('#nameLowerCase#.index');
    }
}

PHP;


        $code = str_replace(
            ['#namespace#', '#name#', '#nameLowerCase#'],
            [$nameSpace, $name, $nameLowerCase],
            $code
        );

        file_put_contents(sprintf('%s/%sController.php', $pathController, $name), $code);

    }

    private function makeDefaultModuleView($name)
    {
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
        $path = sprintf('%s/%s', $this->getPathOfModules(), $inflector->classify($name));
        $viewPath = sprintf('%s/%s', $path, self::SUB_FOLDERS[2]);
        $nameSpace = $this->getNamespaceOfModules();
        $nameLowerCase = ltrim(strtolower(preg_replace('/[A-Z]/', '-$0', $name )), '-');
        $viewFileNamePath = sprintf('%s/%s', $viewPath, $nameLowerCase);

        if (!is_dir($viewPath)) {
            File::makeDirectory($viewPath);
        }

        if (!is_dir($viewFileNamePath)) {
            File::makeDirectory($viewFileNamePath);
        }

        $code = <<<BLADE
<b>Hello its me! edit now -> #filename#</b>
BLADE;


        $code = str_replace(
            ['#filename#'],
            ['{{$filename}}'],
            $code
        );

        file_put_contents(sprintf('%s/index.blade.php', $viewFileNamePath), $code);
    }

    private function makeDefaultModuleFile($name)
    {
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
        $path = sprintf('%s/%s', $this->getPathOfModules(), $inflector->classify($name));
        $nameSpace = $this->getNamespaceOfModules();
        $nameLowerCase = ltrim(strtolower(preg_replace('/[A-Z]/', '-$0', $name )), '-');

        //Module root

        $code = <<<PHP
<?php 

namespace #namespace#\#name#;

use Havennow\LaravelModule\Contracts\ModuleAbstract;
use Illuminate\Contracts\Routing\Registrar;

class Module extends ModuleAbstract 
{

    /**
     * Loads modules composers. if enabled
     *
     * @return void
     */
    //protected function loadComposers()
    //{
    //    view()->composer('#nameLowerCase#.*', #namespaceInfile# . '\Composers\#name#Composer');
    //}

    public function bindRoutes(Registrar #router#)
    {
        #router#->group(['middleware' => ['web', 'auth'], 'prefix' => '#nameLowerCase#'], function (Registrar #router#) {
            #router#->get('/', '#name#Controller@index')->name('#nameLowerCase#.index');
        });
    }
}
PHP;


        $code = str_replace(
            ['#namespace#', '#nameLowerCase#', '#name#','#router#', '#namespaceInfile#'],
            [$nameSpace, $nameLowerCase, $name, '$router', '$this->namespace'],
            $code
        );

        file_put_contents(sprintf('%s/Module.php', $path), $code);

    }

    private function makeModuleFolder()
    {
        $path = $this->getPathOfModules();

        if (empty($path)) {
            $this->warn('Config missing, please run : php artisan vendor:publish --provider="Havennow\LaravelModule\ModuleProvider" --tag=config');

            return;
        }

        if (!is_dir($path)) {
            File::makeDirectory($path);
            $this->info('Created module path : ' . $path);
        } else {
            $this->info('Has already been created :' . $path);
        }
    }
}
