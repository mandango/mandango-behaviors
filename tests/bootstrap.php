<?php

$config = require(__DIR__.'/config.php');
// autoloader
require($config['symfony_dir'].'/src/Symfony/Component/ClassLoader/UniversalClassLoader.php');

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Mandango\Tests'    => __DIR__,
    'Mandango\Behavior' => __DIR__.'/../src',
    'Mandango\Mondator' => $config['mondator_dir'].'/src',
    'Mandango'          => $config['mandango_dir'].'/src',
    'Model'             => __DIR__,
));
$loader->register();

// mondator
$configClasses = array(
    'Model\Hashable' => array(
        'fields' => array(
            'field' => 'string',
        ),
        'behaviors' => array(
            array(
                'class' => 'Mandango\Behavior\Hashable',
            ),
        ),
    ),
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
    'Model\Sluggable' => array(
        'fields' => array(
            'title' => 'string',
        ),
        'behaviors' => array(
            array(
                'class'   => 'Mandango\Behavior\Sluggable',
                'options' => array(
                    'from_field' => 'title',
                ),
            )
        ),
    ),
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
        'metadata_class'  => 'Model\Mapping\Metadata',
        'metadata_output' => __DIR__.'/Model/Mapping',
        'default_output'  => __DIR__.'/Model',
    )),
));
$mondator->process();
