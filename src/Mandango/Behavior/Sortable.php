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
use Mandango\Twig\Mandango as MandangoTwig;

/**
 * Sortable.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Sortable extends ClassExtension
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->addOptions(array(
            'field'        => 'position',
            'new_position' => 'bottom',
            'scope'        => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doConfigClassProcess()
    {
        $this->configClass['fields'][$this->getOption('field')] = 'integer';
        $this->configClass['events']['preInsert'][] = 'sortableSetPosition';
        $this->configClass['events']['preUpdate'][] = 'sortableSetPosition';
        $this->configClass['events']['preDelete'][] = 'sortableRemovePosition';
    }

    /**
     * {@inheritdoc}
     */
    protected function doClassProcess()
    {
        // new position
        if (!in_array($this->getOption('new_position'), array('top', 'bottom'))) {
            throw new \RuntimeException(sprintf('The new_position "%s" is not valid.', $this->getOption('new_position')));
        }

        $this->processTemplate($this->definitions['document_base'],
            file_get_contents(__DIR__.'/templates/SortableDocument.php.twig')
        );
        $this->processTemplate($this->definitions['repository_base'],
            file_get_contents(__DIR__.'/templates/SortableRepository.php.twig')
        );
    }

    protected function configureTwig(\Twig_Environment $twig)
    {
        $twig->addExtension(new MandangoTwig());
    }
}
