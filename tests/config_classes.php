<?php

return array(
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
    'Model\SortableScopeReference' => array(
        'fields' => array(
            'name' => 'string',
        ),
        'referencesOne' => array(
            'sortable' => array('class' => 'Model\Sortable'),
        ),
        'behaviors' => array(
            array('class' => 'Mandango\Behavior\Sortable', 'options' => array('scope' => array('sortable'))),
        ),
    ),
    'Model\SortableSkip' => array(
        'fields' => array(
            'name' => 'string',
        ),
        'behaviors' => array(
            array('class' => 'Mandango\Behavior\Sortable'),
        ),
    ),
    'Model\SortableParent' => array(
        'inheritable' => array('type' => 'single'),
        'fields' => array(
            'name' => 'string',
        ),
        'behaviors' => array(
            array('class' => 'Mandango\Behavior\Sortable'),
        ),
    ),
    'Model\SortableChild' => array(
        'inheritance' => array('class' => 'Model\SortableParent', 'value' => 'child'),
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