<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
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
