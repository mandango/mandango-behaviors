<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mondongo\Tests\Behavior;

use Mandango\Tests\TestCase;

class SluggableTest extends TestCase
{
    public function testSluggable()
    {
        $documents = array();

        $documents[1] = $this->mandango->create('Model\Sluggable');
        $documents[1]->setTitle(' Testing Sluggable Extensión ');
        $documents[1]->save();

        $this->assertSame('testing-sluggable-extension', $documents[1]->getSlug());

        $documents[2] = $this->mandango->create('Model\Sluggable');
        $documents[2]->setTitle(' Testing Sluggable Extensión ');
        $documents[2]->save();

        $this->assertSame('testing-sluggable-extension-2', $documents[2]->getSlug());
    }

    public function testRepositoryFindOneBySlug()
    {
        $documents = array();
        for ($i = 0; $i < 9; $i++) {
            $documents[$i] = $document = $this->mandango->create('Model\Sluggable');
            $document->setTitle('foo');
            $document->save();
        }

        $repository = $this->mandango->getRepository('Model\Sluggable');

        $this->assertSame($documents[3], $repository->findOneBySlug($documents[3]->getSlug()));
        $this->assertSame($documents[6], $repository->findOneBySlug($documents[6]->getSlug()));
    }
}
