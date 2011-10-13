<?php

/*
 * Copyright 2010 Pablo Díez Pascual <pablodip@gmail.com>
 *
 * This file is part of Mandango-behaviors.
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
 * along with mandango. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mandango\Behavior;

use Mandango\Mondator\ClassExtension;
use Mandango\Mondator\Definition\Method;


/**
 * The doctrator Sortable behavior.
 *
 * @package Doctrator
 * @author Alex <pablodip@gmail.com>
 */
class Sortable extends ClassExtension
{
    protected $field;
    protected $fieldSetter;
    protected $fieldGetter;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->addOptions(array(
            'field'       => 'position',
            'new_position' => 'bottom',
        ));
    }

    /**
     * @inheritdoc
     */
    protected function doConfigClassProcess()
    {
        $field = $this->getOption('field');
 		$this->configClass['fields'][$field] = 'integer';
        $this->configClass['events']['preInsert'][] = 'sortableSetPosition';
        $this->configClass['events']['preUpdate'][]  = 'sortableSetPosition';
    }

    /**
     * @inheritdoc
     */
    protected function doClassProcess()
    {
        // new position
        if (!in_array($this->getOption('new_position'), array('top', 'bottom'))) {
            throw new \RuntimeException(sprintf('The new_position "%s" is not valid.', $this->getOption('new_position')));
        }

        // position field
        $this->field       = $this->getOption('field');
        $this->fieldSetter = 'set'.ucfirst($this->field);
        $this->fieldGetter = 'get'.ucfirst($this->field);

        // methods
        $this->processEntityIsFirstMethod();
        $this->processEntityIsLastMethod();
        $this->processEntityGetNextMethod();
        $this->processEntityGetPreviousMethod();
        $this->processEntitySwapWithMethod();
        $this->processEntityMoveUpMethod();
        $this->processEntityMoveDownMethod();
        $this->processRepositoryGetMinPositionMethod();
        $this->processRepositoryGetMaxPositionMethod();

        // events
        $this->processSortableSetPositionMethod();
    }
	
	

    /*
     * "isFirst" entity method
     */
    protected function processEntityIsFirstMethod()
    {
        $method = new Method('public', 'isFirst', '', <<<EOF
        return \$this->{$this->fieldGetter}() === \$this->getRepository()->getMinPosition();
EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Returns if the entity is the first.
     *
     * @return bool Returns if the entity is the first.
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
    }

    /*
     * "isLast" entity method
     */
    protected function processEntityIsLastMethod()
    {
        $method = new Method('public', 'isLast', '', <<<EOF
        return \$this->{$this->fieldGetter}() === \$this->getRepository()->getMaxPosition();
EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Returns if the entity is the last.
     *
     * @return bool Returns if the entity is the last.
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
    }

    /*
     * "getNext" entity method
     */
    protected function processEntityGetNextMethod()
    {
        $method = new Method('public', 'getNext', '', <<<EOF
		\$query = \$this->getRepository()->createQuery();
		\$query->criteria(array('{$this->field}' => \$this->{$this->fieldGetter}() + 1)); 
		\$results = \$query->one();
        return \$results ? \$results : false;

EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Returns the next entity.
     *
     * @return mixed The next entity if exists, if not false.
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
    }

    /*
     * "getPrevious" entity method
     */
    protected function processEntityGetPreviousMethod()
    {
        $method = new Method('public', 'getPrevious', '', <<<EOF
        
		\$query = \$this->getRepository()->createQuery();
		\$query->criteria(array('{$this->field}' => \$this->{$this->fieldGetter}() - 1)); 
		\$results = \$query->one();
        return \$results ? \$results : false;

EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Returns the previous entity.
     *
     * @return mixed The previous entity if exists, if not false.
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
    }

    /*
     * "swapWith" entity method
     */
    protected function processEntitySwapWithMethod()
    {
        $method = new Method('public', 'swapWith', '$document', <<<EOF
        if (!\$document instanceof \\{$this->class}) {
            throw new \InvalidArgumentException('The entity is not an instance of \\{$this->class}.');
        }

        \$oldPosition = \$this->{$this->fieldGetter}();
        \$newPosition = \$document->{$this->fieldGetter}();
		
		\$result  = \$this->getRepository()->findOneById(\$this->getId());
		if(\$result):
		
			\$result->{$this->fieldSetter}(\$newPosition);
			\$query = \$result->queryForSave();
			\$this->getRepository()->getCollection()->update(array('_id' => \$result->getId()), \$query);		
		endif;

		\$result  = \$this->getRepository()->findOneById(\$document->getId());
		if(\$result):
			\$result->{$this->fieldSetter}(\$oldPosition);
			\$query = \$result->queryForSave();
			\$this->getRepository()->getCollection()->update(array('_id' => \$result->getId()), \$query);		
		endif;

EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Swap the position with another entity.
     *
     * @param \\{$this->class} \$entity The entity.
     *
     * @return void
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
    }

    /*
     * "moveUp" entity method
     */
    protected function processEntityMoveUpMethod()
    {
        $method = new Method('public', 'moveUp', '', <<<EOF
        if (\$this->isFirst()) {
            throw new \RuntimeException('The entity is the first.');
        }

        \$this->swapWith(\$this->getPrevious());
EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Move up the entity.
     *
     * @return void
     *
     * @throws \RuntimeException If the entity is the first.
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
    }

    /*
     * "moveDown" entity method
     */
    protected function processEntityMoveDownMethod()
    {
        $method = new Method('public', 'moveDown', '', <<<EOF
        if (\$this->isLast()) {
            throw new \RuntimeException('The entity is the last.');
        }

        \$this->swapWith(\$this->getNext());
EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Move down the entity.
     *
     * @return void
     *
     * @throws \RuntimeException If the entity is the last.
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
    }

    /*
     * "getMinPosition" repository method
     */
    protected function processRepositoryGetMinPositionMethod()
    {
        $method = new Method('public', 'getMinPosition', '', <<<EOF
		
		\$position = false;
		\$result = \$this->createQuery();
		\$result->sort(array('{$this->field}'=>1));
		\$result->limit(1)->one();
		
		
		foreach(\$result as \$r):
			\$position =  \$r->{$this->fieldGetter}();
		endforeach;
		
		
       if( \$position!== false )  return \$result ? (int) \$position : null;
       else return null;


EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Returns the min position.
     *
     * @return integer The min position.
     */
EOF
        );

        $this->definitions['repository_base']->addMethod($method);
    }

    /*
     * "getMaxPosition" repository method
     */
    protected function processRepositoryGetMaxPositionMethod()
    {
        $method = new Method('public', 'getMaxPosition', '', <<<EOF
		
		\$position = false;
		\$result = \$this->createQuery();
		\$result->sort(array('{$this->field}'=>-1));
		\$result->limit(1)->one();
		
		
		foreach(\$result as \$r):
			\$position =  \$r->{$this->fieldGetter}();
		endforeach;
		
		
       if( \$position!== false )  return \$result ? (int) \$position : null;
       else return null;

EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Returns the max position.
     *
     * @return integer The max position.
     */
EOF
        );

        $this->definitions['repository_base']->addMethod($method);
    }

    /*
     * sortableSetPosition method
     */
    protected function processSortableSetPositionMethod()
    {
        $positionAsNew = 'top' == $this->getOption('new_position') ? '1' : '$maxPosition + 1';

        $method = new Method('public', 'sortableSetPosition', '', <<<EOF
        \$maxPosition = \$this->getRepository()->getMaxPosition();
        if (\$this->isNew()):
            \$position = \$maxPosition + 1;
        else:
            if (\$this->isFieldModified('{$this->field}') === false):
                return;
            endif;
			\$changeSet = \$this->getDocumentData();
            \$oldPosition = \$this->getOriginalFieldValue('{$this->field}');
            \$position    = \$changeSet['fields']['{$this->field}'];
        endif;

        // move entities
        if (\$this->isNew()):	
			\$query = \$this->getRepository()->createQuery();
			\$query->criteria(array('{$this->field}' => array ('\$gte' =>\$position))); 
			\$results = \$query->all();
			if(\$results):
				foreach(\$result as \$r):
					\$r->{$this->fieldSetter}(\$r->{$this->fieldGetter}()+1);
					\$query = \$r->queryForSave();
					\$this->getRepository()->getCollection()->update(array('_id' => \$r->getId()), \$query);		
				endforeach;	
			endif;
		 else:
			\$sign = \$position > \$oldPosition ? '-' : '+';
			\$min = min(\$position, \$oldPosition);
			\$max = max(\$position, \$oldPosition);
			\$query = \$this->getRepository()->createQuery();
			
			if(\$sign == '-' ) \$query->criteria(array('{$this->field}' => array ('\$gt' =>\$min, '\$lte' => \$max ))); 
			else \$query->criteria(array('{$this->field}' => array ('\$gte' =>\$min, '\$lt' => \$max ))); 
			
			\$results = \$query->all();
			if(\$results):
				foreach(\$results as \$r):
					if(\$sign == '-' ) \$r->{$this->fieldSetter}(\$r->{$this->fieldGetter}() - 1);
 					else  \$r->{$this->fieldSetter}(\$r->{$this->fieldGetter}() + 1);
					\$query = \$r->queryForSave();
					\$this->getRepository()->getCollection()->update(array('_id' => \$r->getId()), \$query);		
				endforeach;	
			endif;
		 endif;
		 \$this->{$this->fieldSetter}(\$position);
       
EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Set the position.
     */
EOF
        );

        $this->definitions['document_base']->addMethod($method);
    }
}
