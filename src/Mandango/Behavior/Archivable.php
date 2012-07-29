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
 * Archivable.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class Archivable extends ClassExtension
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->addOptions(array(
            'archive_class'     => '%class%Archive',
            'id_field'          => 'documentId',
            'archived_at_field' => 'archivedAt',
            'archive_on_insert' => false,
            'archive_on_update' => false,
            'archive_on_delete' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doNewConfigClassesProcess()
    {
        $this->newConfigClasses[$this->getArchiveClass()] = $this->getArchiveConfigClass();
    }

    private function getArchiveConfigClass()
    {
        return array(
            'fields' => array_merge($this->configClass['fields'], array(
                $this->getOption('id_field')          => 'string',
                $this->getOption('archived_at_field') => 'date',
            )),
            'referencesOne'  => isset($this->configClass['referencesOne'])
                                ? $this->configClass['referencesOne']
                                : null,
            'referencesMany' => isset($this->configClass['referencesMany'])
                                ? $this->configClass['referencesMany']
                                : null,
        );
    }

    private function removeIdGenerator($configClass)
    {
        unset($configClass['idGenerator']);

        return $configClass;
    }

    private function removeArchiveExtension($configClass)
    {
        foreach ($configClass['behaviors'] as $key => $value) {
            if ($value['class'] === 'Mandango\Behavior\Archivable') {
                unset($configClass['behaviors'][$key]);
            }
        }

        return $configClass;
    }

    private function addIdField($configClass)
    {
        $configClass['fields'][$this->getOption('id_field')] = 'string';

        return $configClass;
    }

    private function addArchivedAtField($configClass)
    {
        $configClass['fields'][$this->getOption('archived_at_field')] = 'date';

        return $configClass;
    }

    /**
     * {@inheritdoc}
     */
    protected function doConfigClassProcess()
    {
        foreach (array('insert', 'update', 'delete') as $action) {
            if ($this->getOption('archive_on_'.$action)) {
                $this->configClass['events']['pre'.ucfirst($action)][] = 'archive';
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doClassProcess()
    {
        $this->processTemplate($this->definitions['document_base'],
            file_get_contents(__DIR__.'/templates/ArchivableDocument.php.twig')
        );
    }

    public function getArchiveClass()
    {
        return str_replace('%class%', $this->class, $this->getOption('archive_class'));
    }

    protected function configureTwig(\Twig_Environment $twig)
    {
        $twig->addExtension(new MandangoTwig());
    }
}
