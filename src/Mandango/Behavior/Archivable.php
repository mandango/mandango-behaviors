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
        if ($this->isNotArchivable()) {
            return;
        }

        $this->newConfigClasses[$this->getArchiveClass()] = $this->getArchiveConfigClass();
    }

    private function getArchiveConfigClass()
    {
        return array(
            'archive' => true,
            'archive_from' => $this->class,
            'output' => isset($this->configClass['output'])
                      ? $this->configClass['output']
                      : null,
            'connection' => isset($this->configClass['connection'])
                          ? $this->configClass['connection']
                          : null,
            'fields' => array_merge(
                            isset($this->configClass['fields'])
                                ? $this->configClass['fields']
                                : array(),
                            array(
                                $this->getOption('id_field')          => 'string',
                                $this->getOption('archived_at_field') => 'date',
                            )
                        ),
            'referencesOne'  => isset($this->configClass['referencesOne'])
                              ? $this->configClass['referencesOne']
                              : array(),
            'referencesMany' => isset($this->configClass['referencesMany'])
                              ? $this->configClass['referencesMany']
                              : array(),
            'behaviors' => array(
                array('class' => 'Mandango\Behavior\Archivable'),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function doConfigClassProcess()
    {
        if ($this->isArchive()) {
            $this->configClass['fields'] = array_merge(
                $this->configClass['fields'],
                $this->configClasses[$this->getArchiveFrom()]['fields']
            );
        }

        if ($this->isNotArchivable()) {
            return;
        }

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
        if ($this->isNotArchivable()) {
            return;
        }

        $this->processTemplate($this->definitions['document_base'],
            file_get_contents(__DIR__.'/templates/ArchivableDocument.php.twig')
        );
    }

    public function getArchiveClass()
    {
        return str_replace('%class%', $this->class, $this->getOption('archive_class'));
    }

    private function isArchive()
    {
        return !empty($this->configClass['archive']);
    }

    private function getArchiveFrom()
    {
        return $this->configClass['archive_from'];
    }

    private function isNotArchivable()
    {
        return !empty($this->configClass['archive'])
            || !empty($this->configClass['inheritance']);
    }

    protected function configureTwig(\Twig_Environment $twig)
    {
        $twig->addExtension(new MandangoTwig());
    }
}