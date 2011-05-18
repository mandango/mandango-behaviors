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

class HashableTest extends TestCase
{
    public function testHashable()
    {
        $document = $this->mandango->create('Model\Hashable');
        $document->setField('foo');
        $document->save();

        $this->assertNotNull($document->getHash());
        $this->assertInternalType('string', $document->getHash());
    }

    public function testRepositoryFindOneByHash()
    {
        $repository = $this->mandango->getRepository('Model\Hashable');

        $documents = array();
        for ($i = 0; $i < 9; $i++) {
            $documents[] = $document = $this->mandango->create('Model\Hashable');
            $document->setField('foo'.$i);
        }
        $repository->save($documents);

        $this->assertSame($documents[3], $repository->findByHash($documents[3]->getHash()));
        $this->assertSame($documents[6], $repository->findByHash($documents[6]->getHash()));
    }

    public function testField()
    {
        $document = $this->mandango->create('Model\HashableField');
        $document->setField('foo');
        $document->save();

        $this->assertNotNull($document->getAnotherField());
        $this->assertInternalType('string', $document->getAnotherField());
    }

    public function testLength()
    {
        $document = $this->mandango->create('Model\HashableLength');
        $document->setField('foo');
        $document->save();

        $this->assertSame(5, strlen($document->getHash()));
    }
}
