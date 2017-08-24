<?php
namespace Finetune\Finetune;

use Finetune\Finetune\Repositories\Site\SiteRepository;
use Illuminate\Support\ServiceProvider;
use \Config;
use \View;

class FinetuneServiceProvider extends ServiceProvider{

    protected $defer = false;
    protected $path = __DIR__.'/../..';

    protected $commands = [
        'Finetune\Finetune\Commands\User',
        'Finetune\Finetune\Commands\Install',
    ];

    protected $middleware = [
        'Restrict' => \Finetune\Finetune\Middleware\Restrict::class,
        'Authenticate' => \Finetune\Finetune\Middleware\Authenticate::class,
        'hasSite' => \Finetune\Finetune\Middleware\SiteChecker::class,
        'role' => \Zizaco\Entrust\Middleware\EntrustRole::class,
        'permission' => \Zizaco\Entrust\Middleware\EntrustPermission::class,
        'ability' => \Zizaco\Entrust\Middleware\EntrustAbility::class,
    ];

    public function register()
    {
        // Repositories
        $this->app->bind('Finetune\Finetune\Repositories\Site\SiteInterface', 'Finetune\Finetune\Repositories\Site\SiteRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Helper\HelperInterface', 'Finetune\Finetune\Repositories\Helper\HelperRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Fields\FieldsInterface', 'Finetune\Finetune\Repositories\Fields\FieldsRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Folders\FoldersInterface', 'Finetune\Finetune\Repositories\Folders\FoldersRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Media\MediaInterface', 'Finetune\Finetune\Repositories\Media\MediaRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Node\NodeInterface', 'Finetune\Finetune\Repositories\Node\NodeRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Packages\PackageInterface', 'Finetune\Finetune\Repositories\Packages\PackageRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Permissions\PermissionsInterface', 'Finetune\Finetune\Repositories\Permissions\PermissionsRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Roles\RolesInterface', 'Finetune\Finetune\Repositories\Roles\RolesRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Snippet\SnippetInterface', 'Finetune\Finetune\Repositories\Snippet\SnippetRepository');
        $this->app->bind('Finetune\Finetune\Repositories\SnippetGroup\SnippetGroupInterface', 'Finetune\Finetune\Repositories\SnippetGroup\SnippetGroupRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Tagging\TaggingInterface', 'Finetune\Finetune\Repositories\Tagging\TaggingRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Type\TypeInterface', 'Finetune\Finetune\Repositories\Type\TypeRepository');
        $this->app->bind('Finetune\Finetune\Repositories\User\UserInterface', 'Finetune\Finetune\Repositories\User\UserRepository');
        $this->app->bind('Finetune\Finetune\Repositories\Render\RenderInterface', 'Finetune\Finetune\Repositories\Render\RenderRepository');

        $this->app->register('Finetune\Finetune\Services\Helper\HelperServiceProvider');
        $this->app->register('Finetune\Finetune\Services\Purifier\PurifierServiceServiceProvider');
        $this->app->register('Finetune\Finetune\Services\Node\NodeServiceServiceProvider');
        $this->app->register('Finetune\Finetune\Services\Snippet\SnippetServiceProvider');
        $this->app->register('Finetune\Finetune\Services\Files\FilesServiceProvider');
        $this->app->register('Finetune\Finetune\Services\Tagging\TaggingServiceProvider');
        $this->app->register('Finetune\Finetune\Services\Gallery\GalleryServiceProvider');
        $this->app->register('Finetune\Finetune\Services\Media\MediaServiceProvider');

        $this->app->register('\Zizaco\Entrust\EntrustServiceProvider');
        $this->app->register('\Lab404\Impersonate\ImpersonateServiceProvider');

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();

        $loader->alias('Files', 'Finetune\Finetune\Services\Files\FilesFacade');
        $loader->alias('Gallery', 'Finetune\Finetune\Services\Gallery\GalleryFacade');
        $loader->alias('Helper', 'Finetune\Finetune\Services\Helper\HelperFacade');
        $loader->alias('Node', 'Finetune\Finetune\Services\Node\NodeFacade');
        $loader->alias('Purifier', 'Finetune\Finetune\Services\Purifier\PurifierFacade');
        $loader->alias('Snippets', 'Finetune\Finetune\Services\Snippet\SnippetFacade');
        $loader->alias('Tagging', 'Finetune\Finetune\Services\Tagging\TaggingFacade');
        $loader->alias('Media', 'Finetune\Finetune\Services\Media\MediaFacade');
        $loader->alias('Entrust', '\Zizaco\Entrust\EntrustFacade');

        $this->commands($this->commands);
    }

    public function boot(\Illuminate\Routing\Router $router, \Illuminate\Contracts\Validation\Factory $validation, \Illuminate\View\Compilers\BladeCompiler $bladeCompiler, \Illuminate\Database\Schema\Builder $schema)
    {
        if ($this->app->runningInConsole()) {

        }
        $this->loadRoutesFrom($this->path.'/Routes/routes.php');

        $this->loadMigrationsFrom($this->path.'/Migrations');

        $this->loadTranslationsFrom($this->path.'/Lang', 'finetune');

        $this->loadViewsFrom($this->path.'/Views/finetune', 'finetune');

        $this->publishes([
            $this->path.'/Config/finetune.php' => config_path('finetune.php'),
            $this->path.'/Config/forms.php' => config_path('forms.php'),
            $this->path.'/Config/image.php' => config_path('image.php'),
            $this->path.'/Config/auth.php' => config_path('auth.php'),
            $this->path.'/Config/ipfilter.php' => config_path('ipfilter.php'),
            $this->path.'/Config/packages.php' => config_path('packages.php'),
            $this->path.'/Config/upload.php' => config_path('upload.php'),
            $this->path.'/Config/purifier.php' => config_path('purifier.php'),
            $this->path.'/Config/entrust.php' => config_path('entrust.php'),
            $this->path.'/Config/bannedtags.php' => config_path('bannedtags.php'),
            $this->path.'/Config/fields.php' => config_path('fields.php'),
            $this->path.'/Lang' => resource_path('lang/vendor/finetune'),
            $this->path.'/Views/finetune' => resource_path('views/vendor/finetune'),
        ]);

        $this->publishes([
           // $this->path.'/Views/themes' => public_path('themes'),
            $this->path.'/Assets' => public_path('finetune/assets'),
            $this->path.'/Public' => public_path('.')
        ], 'public');


        foreach($this->middleware as $name => $class) {
            $router->aliasMiddleware($name, $class);
        }

        $validation->extend('name_validator', function ($attribute, $value, $parameters, $validator) {

            $banned = config('bannedtags');
            $names = explode(':', $value);
            foreach ($names as $name) {
                if (in_array($name, $banned)) {
                    return false;
                }
            }
            return true;
        });

        $schema->defaultStringLength(191);

        $bladeCompiler->extend(function($view, $compiler)
        {
            $pattern = "/(?<!\w)(\s*)@var\(\s*'([A-Za-z1-9_]*)',\s*(.*)\)/";
            $view = preg_replace($pattern, "<?php \$$2 = $3 ?>", $view);
            return $view;
        });


        if (strpos(php_sapi_name(), 'cli') === false) {
            $siteRepo = resolve('Finetune\Finetune\Repositories\Site\SiteInterface');
            $site = $siteRepo->getSite($this->app->request);

            $bladeCompiler->directive('group', function ($expression) use ($site) {
                $expression = str_replace('(', '', $expression);
                $expression = str_replace(')', '', $expression);
                $expression = str_replace("'", '', $expression);
                $expression = str_replace('"', '', $expression);
                return \Snippets::renderGroup($site, $expression);
            });

            $bladeCompiler->directive('snippet', function ($expression) use ($site) {
                $expression = str_replace('(', '', $expression);
                $expression = str_replace(')', '', $expression);
                $expression = str_replace("'", '', $expression);
                $expression = str_replace('"', '', $expression);
                return \Snippets::renderSnippet($site, $expression);
            });

            $bladeCompiler->directive('gallery', function ($expression) use ($site) {
                $expression = str_replace('(', '', $expression);
                $expression = str_replace(')', '', $expression);
                $expression = str_replace("'", '', $expression);
                $expression = str_replace('"', '', $expression);
                return \Gallery::renderGallery($site, $expression);
            });

            $bladeCompiler->directive('filebank', function ($expression) use ($site) {
                $expression = str_replace('(', '', $expression);
                $expression = str_replace(')', '', $expression);
                $expression = str_replace("'", '', $expression);
                $expression = str_replace('"', '', $expression);
                return \Files::renderFileBank($site, $expression);
            });
        }
    }
}