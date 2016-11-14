<?php

namespace IramGutierrez\API\Generators;

use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\Object;
use Memio\Model\Method;
use Memio\Model\Argument;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Phpdoc\LicensePhpdoc;
use Memio\Model\Phpdoc\Description;
use Memio\Model\Phpdoc\MethodPhpdoc;
use Illuminate\Support\Facades\File as FileSystem;

class ControllerGenerator extends BaseGenerator{

    protected $prefix;

    protected $documentation;

    protected $pathname;

    protected $pathfile;

    protected $layer = 'Controller';

    public function setPath($pathname)
    {
        $this->pathname = $pathname;

        $this->pathfile = $this->pathname;

        $this->appNamespace = $this->getAppNamespace().$this->pathname.'\\';

        $this->namespace = $this->getAppNamespace().'Http\\Controllers\\'.$this->pathname.'\\';

        $this->path = app_path().'/Http/Controllers/';

    }

    public function generate()
    {
        /*GENERATE BASE CONTROLLER FOR NAMESPACE */
        if(!FileSystem::exists($this->path.$this->pathname.'/BaseController.php'))
        {
          $baseDistPath = realpath(__DIR__.'/../Controllers/BaseController.php.dist');
          $basePath = realpath(__DIR__.'/../Controllers/').'/BaseController.php';
          $copy = FileSystem::copy($baseDistPath , $basePath);

          $base = realpath(__DIR__.'/../Controllers/BaseController.php');

          $find = 'use App\Http\Controllers\Controller;';

          $replace = 'use '.$this->getAppNamespace().'Http\Controllers\Controller;';

          $find2 = 'namespace AppNamespace\Http\Controllers\API;';

          $replace2 = 'namespace '.$this->getAppNamespace().'Http\Controllers\\'.$this->pathname.';';

          FileSystem::put($base , str_replace($find2 , $replace2 , str_replace($find , $replace , file_get_contents($base))));

          if(!FileSystem::isDirectory($this->path.$this->pathname))
          {
              FileSystem::makeDirectory($this->path.$this->pathname);
          }

          FileSystem::move($base , $this->path.$this->pathname.'/BaseController.php');

        }

        $prefix = ($this->prefix) ? $this->prefix.'/' : '';

        $repository = File::make($this->filename)
            ->setLicensePhpdoc(new LicensePhpdoc(self::PROJECT_NAME, self::AUTHOR_NAME, self::AUTHOR_EMAIL))
            ->addFullyQualifiedName(new FullyQualifiedName(\Illuminate\Http\Request::class))
            ->addFullyQualifiedName(new FullyQualifiedName($this->namespace."BaseController"))
            ->addFullyQualifiedName(new FullyQualifiedName($this->appNamespace."Repositories\\".$this->entity."Repository as Repository"))
            ->addFullyQualifiedName(new FullyQualifiedName($this->appNamespace."Managers\\".$this->entity."Manager as Manager"))
            ->setStructure(
                Object::make($this->namespace.$this->entity.$this->layer)
                    ->extend(new Object(BaseController::class))
                    ->addMethod(
                        Method::make('__construct')
                            ->addArgument(new Argument('Repository', 'Repository'))
                            ->addArgument(new Argument('Manager', 'Manager'))
                            ->setBody('        return parent::__construct($Repository , $Manager);')
                    )
                    ->addMethod(
                        Method::make('index')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('@api {get} /'.$prefix.snake_case(str_plural($this->entity)).' 1 Request all '.snake_case(str_plural($this->entity)))
                                    ->addLine('@apiVersion 1.0.0')
                                    ->addLine('@apiName All'.str_plural($this->entity))
                                    ->addLine('@apiGroup '.str_plural($this->entity))
                                    ->addEmptyLine()
                                    ->addLine('@apiSuccess {Object[]}  0 '.$this->entity.' Object.')
                                    ->addLine('@apiSuccess {Number} 0.id Id.')
                                    ->addLine('@apiSuccess {DateTime} 0.created_at  Created date.')
                                    ->addLine('@apiSuccess {DateTime} 0.updated_at  Last modification date.')
                                    ->addEmptyLine()
                                    ->addLine("@apiSuccessExample Success 200 Example")
                                    ->addLine(" HTTP/1.1 200 OK")
                                    ->addLine("[")
                                    ->addLine("    {")
                                    ->addLine('        id: 1,')
                                    ->addLine('        created_at: '.date('Y-m-d h:i:s').',')
                                    ->addLine('        updated_at: '.date('Y-m-d h:i:s'))
                                    ->addLine("    },")
                                    ->addLine("    {")
                                    ->addLine('        id: 2,')
                                    ->addLine('        created_at: '.date('Y-m-d h:i:s').',')
                                    ->addLine('        updated_at: '.date('Y-m-d h:i:s'))
                                    ->addLine("    }")
                                    ->addLine("]")
                                    ->addEmptyLine()
                                    ->addLine("@apiError (ServerError 500) {string} error Server error.")
                                    ->addEmptyLine()
                                    ->addLine("@apiErrorExample {json} ServerError 500 Example")
                                    ->addLine("  HTTP/1.1 404 Not Found")
                                    ->addLine("{")
                                    ->addLine('    error: Server error. Try again.')
                                    ->addLine("}")
                                )

                            )
                            ->addArgument(new Argument('Request', 'Request'))
                            ->setBody('        return parent::index($Request);')
                    )
                    ->addMethod(
                        Method::make('store')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('@api {post} /'.$prefix.snake_case(str_plural($this->entity)).' 3 Store a  '.snake_case($this->entity))
                                    ->addLine('@apiVersion 1.0.0')
                                    ->addLine('@apiName Store'.$this->entity)
                                    ->addLine('@apiGroup '.str_plural($this->entity))
                                    ->addEmptyLine()
                                    ->addLine('@apiParam (FormData) {String} name '.$this->entity.' name.')
                                    ->addEmptyLine()
                                    ->addLine('@apiSuccess (Success 201) {Number} id Id.')
                                    ->addLine('@apiSuccess (Success 201)  {DateTime} created_at  Created date.')
                                    ->addLine('@apiSuccess (Success 201)  {DateTime} updated_at  Last modification date.')
                                    ->addEmptyLine()
                                    ->addLine("@apiSuccessExample {json} Success 201 Example")
                                    ->addLine(" HTTP/1.1 201 OK")
                                    ->addLine("{")
                                    ->addLine('    id: 1,')
                                    ->addLine('    created_at: '.date('Y-m-d h:i:s').',')
                                    ->addLine('    updated_at: '.date('Y-m-d h:i:s'))
                                    ->addLine("}")
                                    ->addEmptyLine()
                                    ->addLine("@apiError (ValidationErrors 400)  {array[]} name  List of errors for name field.")
                                    ->addLine("@apiError (ValidationErrors 400)  {string} name.0  First error for name.")
                                    ->addLine("@apiError (ValidationErrors 400)  {string} name.1  Second error for name.")
                                    ->addEmptyLine()
                                    ->addLine("@apiErrorExample {json} ValidationErrors 400 Example")
                                    ->addLine("  HTTP/1.1 400 Bad Request")
                                    ->addLine("{")
                                    ->addLine('    errors: {')
                                    ->addLine('         name: The name field is required')
                                    ->addLine("    }")
                                    ->addLine("}")
                                    ->addEmptyLine()
                                    ->addLine("@apiError (ServerError 500) {string} error Server error.")
                                    ->addEmptyLine()
                                    ->addLine("@apiErrorExample {json} ServerError 500 Example")
                                    ->addLine("  HTTP/1.1 404 Not Found")
                                    ->addLine("{")
                                    ->addLine('    error: Server error. Try again.')
                                    ->addLine("}")
                                )

                            )
                            ->addArgument(new Argument('Request', 'Request'))
                            ->setBody('        return parent::store($Request);')
                    )
                    ->addMethod(
                        Method::make('show')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('@api {get} /'.$prefix.snake_case(str_plural($this->entity)).'/:id 2 Request a specific '.snake_case($this->entity))
                                    ->addLine('@apiVersion 1.0.0')
                                    ->addLine('@apiName Get'.$this->entity)
                                    ->addLine('@apiGroup '.str_plural($this->entity))
                                    ->addEmptyLine()
                                    ->addLine('@apiParam (Url params) {Number} id '.$this->entity.' unique id.')
                                    ->addEmptyLine()
                                    ->addLine('@apiSuccess {Number} id Id.')
                                    ->addLine('@apiSuccess {DateTime} created_at  Created date.')
                                    ->addLine('@apiSuccess {DateTime} updated_at  Last modification date.')
                                    ->addEmptyLine()
                                    ->addLine("@apiSuccessExample {json} Success 200 Example")
                                    ->addLine(" HTTP/1.1 200 OK")
                                    ->addLine("{")
                                    ->addLine('    id: 1,')
                                    ->addLine('    created_at: '.date('Y-m-d h:i:s').',')
                                    ->addLine('    updated_at: '.date('Y-m-d h:i:s'))
                                    ->addLine("}")
                                    ->addEmptyLine()
                                    ->addLine("@apiError (EntityNotFound 404) {string} error The id of the ".snake_case($this->entity)." was not found.")
                                    ->addEmptyLine()
                                    ->addLine("@apiErrorExample {json} EntityNotFound 404 Example")
                                    ->addLine("  HTTP/1.1 404 Not Found")
                                    ->addLine("{")
                                    ->addLine('    error: Entity not found')
                                    ->addLine("}")
                                    ->addEmptyLine()
                                    ->addLine("@apiError (ServerError 500) {string} error Server error.")
                                    ->addEmptyLine()
                                    ->addLine("@apiErrorExample {json} ServerError 500 Example")
                                    ->addLine("  HTTP/1.1 404 Not Found")
                                    ->addLine("{")
                                    ->addLine('    error: Server error. Try again.')
                                    ->addLine("}")
                                )

                            )
                            ->addArgument(new Argument('Request', 'Request'))
                            ->addArgument(new Argument('integer','id'))
                            ->setBody('        return parent::show($Request , $id);')
                    )
                    ->addMethod(
                        Method::make('update')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('@api {put} /'.$prefix.snake_case(str_plural($this->entity)).'/:id 4 Update a specific  '.snake_case($this->entity))
                                    ->addLine('@apiVersion 1.0.0')
                                    ->addLine('@apiName Update'.$this->entity)
                                    ->addLine('@apiGroup '.str_plural($this->entity))
                                    ->addEmptyLine()
                                    ->addLine('@apiParam (Url params)  {Number} id '.$this->entity.' unique id.')
                                    ->addLine('@apiParam (FormData) {String} name '.$this->entity.' name.')
                                    ->addEmptyLine()
                                    ->addLine('@apiSuccess {Number} id Id.')
                                    ->addLine('@apiSuccess {DateTime} created_at  Created date.')
                                    ->addLine('@apiSuccess {DateTime} updated_at  Last modification date.')
                                    ->addEmptyLine()
                                    ->addLine("@apiSuccessExample {json} Success 200 Example")
                                    ->addLine(" HTTP/1.1 200 OK")
                                    ->addLine("{")
                                    ->addLine('    id: 1,')
                                    ->addLine('    created_at: '.date('Y-m-d h:i:s').',')
                                    ->addLine('    updated_at: '.date('Y-m-d h:i:s'))
                                    ->addLine("}")
                                    ->addEmptyLine()
                                    ->addLine("@apiError (EntityNotFound 404) {string} error The id of the ".snake_case($this->entity)." was not found.")
                                    ->addEmptyLine()
                                    ->addLine("@apiErrorExample {json} EntityNotFound 404 Example")
                                    ->addLine("  HTTP/1.1 404 Not Found")
                                    ->addLine("{")
                                    ->addLine('    error: Entity not found')
                                    ->addLine("}")
                                    ->addEmptyLine()
                                    ->addLine("@apiError (ValidationErrors 400)  {array[]} name  List of errors for name field.")
                                    ->addLine("@apiError (ValidationErrors 400)  {string} name.0  First error for name.")
                                    ->addLine("@apiError (ValidationErrors 400)  {string} name.1  Second error for name.")
                                    ->addEmptyLine()
                                    ->addLine("@apiErrorExample {json} ValidationErrors 400 Example")
                                    ->addLine("  HTTP/1.1 400 Bad Request")
                                    ->addLine("{")
                                    ->addLine('    errors: {')
                                    ->addLine('         name: The name field is required')
                                    ->addLine("    }")
                                    ->addLine("}")
                                    ->addEmptyLine()
                                    ->addLine("@apiError (ServerError 500) {string} error Server error.")
                                    ->addEmptyLine()
                                    ->addLine("@apiErrorExample {json} ServerError 500 Example")
                                    ->addLine("  HTTP/1.1 404 Not Found")
                                    ->addLine("{")
                                    ->addLine('    error: Server error. Try again.')
                                    ->addLine("}")
                                )

                            )
                            ->addArgument(new Argument('Request', 'Request'))
                            ->addArgument(new Argument('integer','id'))
                            ->setBody('        return parent::update($Request , $id);')
                    )
                    ->addMethod(
                        Method::make('destroy')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('@api {delete} /'.$prefix.snake_case(str_plural($this->entity)).'/:id 5 Delete a specific  '.snake_case($this->entity))
                                    ->addLine('@apiVersion 1.0.0')
                                    ->addLine('@apiName Delete'.$this->entity)
                                    ->addLine('@apiGroup '.str_plural($this->entity))
                                    ->addEmptyLine()
                                    ->addLine('@apiParam (Url params)  {Number} id '.$this->entity.' unique id.')
                                    ->addEmptyLine()
                                    ->addLine("@apiSuccessExample Success 204 Example")
                                    ->addLine(" HTTP/1.1 204 OK")
                                    ->addEmptyLine()
                                    ->addLine("@apiError (EntityNotFound 404) {string} error The id of the ".snake_case($this->entity)." was not found.")
                                    ->addEmptyLine()
                                    ->addLine("@apiErrorExample {json} EntityNotFound 404 Example")
                                    ->addLine("  HTTP/1.1 404 Not Found")
                                    ->addLine("{")
                                    ->addLine('    error: Entity not found')
                                    ->addLine("}")
                                    ->addEmptyLine()
                                    ->addLine("@apiError (ServerError 500) {string} error Server error.")
                                    ->addEmptyLine()
                                    ->addLine("@apiErrorExample {json} ServerError 500 Example")
                                    ->addLine("  HTTP/1.1 404 Not Found")
                                    ->addLine("{")
                                    ->addLine('    error: Server error. Try again.')
                                    ->addLine("}")
                                )

                            )
                            ->addArgument(new Argument('Request', 'Request'))
                            ->addArgument(new Argument('integer','id'))
                            ->setBody('        return parent::destroy($Request , $id);')
                    )
            );

        $prettyPrinter = Build::prettyPrinter();
        $generatedCode = $prettyPrinter->generateCode($repository);

        return $this->generateFile($generatedCode);
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function setDocumentation($documentation)
    {
        $this->documentation = $documentation;
    }
}
