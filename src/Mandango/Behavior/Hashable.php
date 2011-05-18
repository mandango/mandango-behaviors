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
 * Hashable.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Hashable extends ClassExtension
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->addOptions(array(
            'field'  => 'hash',
            'length' => 10
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doConfigClassProcess()
    {
        $this->configClass['fields'][$this->getOption('field')] = 'string';
        $this->configClass['events']['preInsert'][] = 'updateHashableHash';
    }

    /**
     * {@inheritdoc}
     */
    protected function doClassProcess()
    {
        $field = $this->getOption('field');
        $length = $this->getOption('length');

        // field
        $this->configClass['fields'][$field] = array('type' => 'string');

        // index
        $this->configClass['indexes'][] = array('keys' => array($field => 1), array('unique' => 1));

        // event
        $fieldSetter = 'set'.ucfirst($field);

        $method = new Method('public', 'updateHashableHash', '', <<<EOF
        do {
            \$hash = '';
            for (\$i = 1; \$i <= $length; \$i++) {
                \$hash .= substr(sha1(microtime(true).mt_rand(111111, 999999)), mt_rand(0, 39), 1);
            };

            \$result = \$this->getRepository()->getCollection()->findOne(array('$field' => \$hash));
        } while (\$result);

        \$this->$fieldSetter(\$hash);
EOF
        );
        $this->definitions['document_base']->addMethod($method);

        // repository ->findOneByHash()
        $method = new Method('public', 'findByHash', '$hash', <<<EOF
        return \$this->createQuery(array('$field' => \$hash))->one();
EOF
        );
        $method->setDocComment(<<<EOF
    /**
     * Returns a document by hash.
     *
     * @param string \$hash The hash.
     *
     * @return mixed The document or null if it does not exist.
     */
EOF
        );
        $this->definitions['repository_base']->addMethod($method);
    }
}
