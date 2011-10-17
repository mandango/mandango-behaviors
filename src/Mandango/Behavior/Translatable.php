<?php

/*
 * Copyright 2010 Pablo Díez Pascual <pablodip@gmail.com>
 *
 * This file is part of Doctrator.
 *
 * Doctrator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Doctrator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Mandango. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mandango\Behavior;

use Mandango\Mondator\ClassExtension;
use Mandango\Mondator\Definition\Method;

/**
 * The Mandango Translatable behavior.
 *
 * @package Mandango
 * @author  Alex
 */
class Translatable extends ClassExtension
{
  
	/**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->addRequiredOption('TranslateFields');
    }
	
    /**
     * @inheritdoc
     */
    protected function doConfigClassProcess()
    {
        $this->configClass['events']['preInsert'][] = 'translatableFunction';
        $this->configClass['events']['preUpdate'][]  = 'translatableFunction';
    }

    /**
     * @inheritdoc
     */
    protected function doNewConfigClassesProcess()
    {
        $translationConfigClass = array(
		    'isEmbedded' => true,
            'fields' => array(
                'locale' => array('type' => 'string', 'length' => 7),
            )
        );

        $configClassColumns = $this->configClass['fields'];
        foreach ($this->getOption('TranslateFields') as $column) {
            if (!isset($configClassColumns[$column])) {
                throw new \RuntimeException(sprintf('The column "%s" of the class "%s" does not exists.', $column, $this->class));
            }
            $translationConfigClass['fields'][$column] = $configClassColumns[$column];
            unset($configClassColumns[$column]);
        }
		
        $this->configClass['fields'] = $configClassColumns;
		
		$translationConfigClass['output'] = $this->configClass['output'];
		$translationConfigClass['bundle_name'] = $this->configClass['bundle_name'];
		$translationConfigClass['bundle_namespace'] = $this->configClass['bundle_namespace'];
		$translationConfigClass['bundle_dir'] = $this->configClass['bundle_dir'];
		
        $this->newConfigClasses[$this->class.'Translation'] = $translationConfigClass;

        // relation
        $this->configClass['embeddedsMany']['translations'] = array(
            'class'  => $this->class.'Translation'
        );
    }

    /**
	 *
     * @inheritdoc  
     */
    protected function doClassProcess()
    {
       
        // events
        $this->processTranslatableFunction();

	    // "translation" method
        $method = new Method('public', 'translation', '$locale', <<<EOF
        foreach (\$this->getTranslations() as \$translation) {
            if (\$translation->getLocale() == \$locale) {
                return \$translation;
            }
        }

		\$mandango  = \$this->getMandango();
		\$translation = \$mandango->create('{$this->class}Translation');
        \$translation->setLocale(\$locale);      

        return \$translation;
EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Returns a translation entity by locale.
     *
     * @param string \$locale The locale.
     *
     * @return \{$this->class}Translation The translation entity.
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
    }
    /*
     * translatableFunction method
     */
    protected function processTranslatableFunction()
    {
       

        $method = new Method('public', 'translatableFunction', '', <<<EOF
     
 		\$a = \$this->getDocumentData();
		foreach(\$a['embeddedsMany']['translations'] as \$m):
			if(\$m->getLocale() == ""){
				throw new \RuntimeException('Locale value is compulsory.');
			}
		endforeach;
      
EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Verify $locale value because it's necessary.
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
		
	}
}
