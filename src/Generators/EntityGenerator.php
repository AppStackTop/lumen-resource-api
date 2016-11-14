<?php

namespace IramGutierrez\API\Generators;

use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\Object;
use Memio\Model\Phpdoc\PropertyPhpdoc;
use Memio\Model\Phpdoc\VariableTag;
use Memio\Model\Property;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Phpdoc\LicensePhpdoc;

use IramGutierrez\API\Entities\BaseEntity;

class EntityGenerator extends BaseGenerator{

    protected $pathfile = 'Entities';

    protected $layer = 'Entity';

    protected $table;

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function generate()
    {
        $entity = File::make($this->filename)
            ->setLicensePhpdoc(
                new LicensePhpdoc(self::PROJECT_NAME, self::AUTHOR_NAME, self::AUTHOR_EMAIL)
            )
            ->addFullyQualifiedName(
                new FullyQualifiedName(\Illuminate\Database\Eloquent\Collection::class)
            )
            ->addFullyQualifiedName(new FullyQualifiedName(BaseEntity::class))
            ->setStructure(
                Object::make($this->namespace.$this->entity.$this->layer)
                    ->extend(
                        new Object(BaseEntity::class)
                    )
                    ->addProperty(
                        Property::make('table')
                            ->setPhpdoc(PropertyPhpdoc::make()
                               ->setVariableTag(new VariableTag('$table'))
                            )
                            ->makeProtected()
                            ->setDefaultValue("'".$this->table."'")
                    )
                    ->addProperty(
                        Property::make('fillable')
                            ->setPhpdoc(PropertyPhpdoc::make()
                                ->setVariableTag(new VariableTag('$fillable'))
                            )
                            ->makeProtected()
                            ->setDefaultValue("['id' , 'name']")
                    )
                    ->addProperty(
                        Property::make('hidden')
                            ->setPhpdoc(PropertyPhpdoc::make()
                                ->setVariableTag(new VariableTag('$hidden'))
                            )
                            ->makeProtected()
                            ->setDefaultValue("[]")
                    )
                    ->addProperty(
                        Property::make('appends')
                            ->setPhpdoc(PropertyPhpdoc::make()
                                ->setVariableTag(new VariableTag('$appends'))
                            )
                            ->makeProtected()
                            ->setDefaultValue("[]")
                    )
            );

        $prettyPrinter = Build::prettyPrinter();
        $generatedCode = $prettyPrinter->generateCode($entity);

        return $this->generateFile($generatedCode);


    }
}