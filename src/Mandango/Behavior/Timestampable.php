<?php

/*
 * Copyright 2010 Pablo Díez <pablodip@gmail.com>
 *
 * This file is part of Mandango.
 *
 * Mandango is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Mandango is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Mandango. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mandango\Behavior;

use Mandango\Inflector;
use Mandango\Mondator\ClassExtension;
use Mandango\Mondator\Definition\Method;

/**
 * Timestampable.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Timestampable extends ClassExtension
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->addOptions(array(
            'created_enabled' => true,
            'created_field'   => 'created_at',
            'updated_enabled' => true,
            'updated_field'   => 'updated_at',
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doConfigClassProcess()
    {
        // created
        if ($this->getOption('created_enabled')) {
            $this->configClass['fields'][$this->getOption('created_field')] = 'date';
            $this->configClass['events']['preInsert'][] = 'updateTimestampableCreated';
        }

        // updated
        if ($this->getOption('updated_enabled')) {
            $this->configClass['fields'][$this->getOption('updated_field')] = 'date';
            $this->configClass['events']['preUpdate'][] = 'updateTimestampableUpdated';
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doClassProcess()
    {
        // created
        if ($this->getOption('created_enabled')) {
            $fieldSetter = 'set'.Inflector::camelize($this->getOption('created_field'));

            $method = new Method('protected', 'updateTimestampableCreated', '', <<<EOF
        \$this->$fieldSetter(new \DateTime());
EOF
            );
            $this->definitions['document_base']->addMethod($method);
        }

        // updated
        if ($this->getOption('updated_enabled')) {
            $fieldSetter = 'set'.Inflector::camelize($this->getOption('updated_field'));

            $method = new Method('protected', 'updateTimestampableUpdated', '', <<<EOF
        \$this->$fieldSetter(new \DateTime());
EOF
            );
            $this->definitions['document_base']->addMethod($method);
        }
    }
}
