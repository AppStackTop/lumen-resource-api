<?php

namespace IramGutierrez\API\Generators;

use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\Object;
use Memio\Model\Property;
use Memio\Model\Method;
use Memio\Model\Argument;
use Memio\Model\Constant;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Phpdoc\LicensePhpdoc;

use IramGutierrez\API\Validators\BaseValidator;

class ValidatorGenerator extends BaseGenerator{

    protected $pathfile = 'Validators';

    protected $layer = 'Validator';

    public function generate()
    {
        $repository = File::make($this->filename)
            ->setLicensePhpdoc(new LicensePhpdoc(self::PROJECT_NAME, self::AUTHOR_NAME, self::AUTHOR_EMAIL))
            ->addFullyQualifiedName(new FullyQualifiedName(BaseValidator::class))
            ->addFullyQualifiedName(new FullyQualifiedName($this->appNamespace."Entities\\".$this->entity."Entity as Entity"))
            ->setStructure(
                Object::make($this->namespace.$this->entity.$this->layer)
                    ->extend(new Object(BaseValidator::class))
                    ->addProperty(
                        Property::make('rules')
                            ->makeProtected()
                            ->setDefaultValue("[\n      'name' => 'required'\n    ]")
                    )
                    ->addMethod(
                        Method::make('__construct')
                            ->addArgument(new Argument('Entity', 'Entity'))
                            ->setBody('        parent::__construct($Entity);')
                    )
            );

        $prettyPrinter = Build::prettyPrinter();
        $generatedCode = $prettyPrinter->generateCode($repository);

        return $this->generateFile($generatedCode);
    }
}
