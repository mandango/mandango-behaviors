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

use Mandango\Mondator\ClassExtension;
use Mandango\Mondator\Definition\Method;

/**
 * Ipable.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Ipable extends ClassExtension
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->addOptions(array(
            'createdEnabled' => true,
            'createdField'   => 'createdFrom',
            'updatedEnabled' => true,
            'updatedField'   => 'updatedFrom',
            'getIpCallable' => array('Mandango\Behavior\Util\IpableUtil', 'getIp'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doConfigClassProcess()
    {
        // created
        if ($this->getOption('createdEnabled')) {
            $this->configClass['fields'][$this->getOption('createdField')] = 'string';
            $this->configClass['events']['preInsert'][] = 'updateIpableCreated';
        }

        // updated
        if ($this->getOption('updatedEnabled')) {
            $this->configClass['fields'][$this->getOption('updatedField')] = 'string';
            $this->configClass['events']['preUpdate'][] = 'updateIpableUpdated';
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doClassProcess()
    {
        // created
        if ($this->getOption('createdEnabled')) {
            $fieldSetter = 'set'.ucfirst($this->getOption('createdField'));
            $getIpCallable = var_export($this->getOption('getIpCallable'), true);

            $method = new Method('protected', 'updateIpableCreated', '', <<<EOF
        \$this->$fieldSetter(call_user_func($getIpCallable));
EOF
            );
            $this->definitions['document_base']->addMethod($method);
        }

       // updated
        if ($this->getOption('updatedEnabled')) {
            $fieldSetter = 'set'.ucfirst($this->getOption('updatedField'));
            $getIpCallable = var_export($this->getOption('getIpCallable'), true);

            $method = new Method('protected', 'updateIpableUpdated', '', <<<EOF
        \$this->$fieldSetter(call_user_func($getIpCallable));
EOF
            );
            $this->definitions['document_base']->addMethod($method);
        }
    }
}
