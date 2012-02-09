<?php

$vendorDir = __DIR__.'/../vendor';

// autoloader
require($vendorDir.'/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php');

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Mandango\Tests'    => __DIR__,
    'Mandango\Behavior' => __DIR__.'/../src',
    'Mandango\Mondator' => $vendorDir.'/mondator/src',
    'Mandango'          => $vendorDir.'/mandango/src',
    'Model'             => __DIR__,
));
$loader->registerPrefixes(array(
    'Twig_' => $vendorDir.'/twig/lib',
));
$loader->register();

// mondator
$configClasses = array(
    // Hashable
    'Model\Hashable' => array(
        'fields' => array(
            'field' => 'string',
        ),
        'behaviors' => array(
            array('class' => 'Mandango\Behavior\Hashable',),
        ),
    ),
    'Model\HashableField' => array(
        'fields' => array(
            'field' => 'string',
        ),
        'behaviors' => array(
            array('class' => 'Mandango\Behavior\Hashable', 'options' => array('field' => 'anotherField')),
        ),
    ),
    'Model\HashableLength' => array(
        'fields' => array(
            'field' => 'string',
        ),
        'behaviors' => array(
            array('class' => 'Mandango\Behavior\Hashable', 'options' => array('length' => 5)),
        ),
    ),
    // Ipable
    'Model\Ipable' => array(
        'fields' => array(
            'field' => 'string',
        ),
        'behaviors' => array(
            array(
                'class' => 'Mandango\Behavior\Ipable',
            ),
        ),
    ),
    // Sluggable
    'Model\Sluggable' => array(
        'fields' => array(
            'title' => 'string',
        ),
        'behaviors' => array(
            array(
                'class'   => 'Mandango\Behavior\Sluggable',
                'options' => array(
                    'fromField' => 'title',
                ),
            )
        ),
    ),
    // Sortable
    'Model\Sortable' => array(
        'fields' => array(
            'name' => 'string',
        ),
        'behaviors' => array(
            array('class' => 'Mandango\Behavior\Sortable'),
        ),
    ),
    'Model\SortableTop' => array(
        'fields' => array(
            'name' => 'string',
        ),
        'behaviors' => array(
            array('class' => 'Mandango\Behavior\Sortable', 'options' => array('new_position' => 'top')),
        ),
    ),
    'Model\SortableScope' => array(
        'fields' => array(
            'type' => 'string',
            'name' => 'string',
        ),
        'behaviors' => array(
            array('class' => 'Mandango\Behavior\Sortable', 'options' => array('scope' => array('type'))),
        ),
    ),
    // Timestampable
    'Model\Timestampable' => array(
        'fields' => array(
            'field' => 'string'
        ),
        'behaviors' => array(
            array(
                'class' => 'Mandango\Behavior\Timestampable',
            )
        ),
    ),
);

use \Mandango\Mondator\Mondator;

$mondator = new Mondator();
$mondator->setConfigClasses($configClasses);
$mondator->setExtensions(array(
    new Mandango\Extension\Core(array(
        'metadata_factory_class'  => 'Model\Mapping\MetadataFactory',
        'metadata_factory_output' => __DIR__.'/Model/Mapping',
        'default_output'          => __DIR__.'/Model',
    )),
));
$mondator->process();
