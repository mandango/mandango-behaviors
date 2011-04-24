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
        $document = new \Model\Hashable();
        $document->setField('foo');
        $document->save();

        $this->assertNotNull($document->getHash());
        $this->assertInternalType('string', $document->getHash());
    }

    public function testRepositoryFindOneByHash()
    {
        $documents = array();
        for ($i = 0; $i < 9; $i++) {
            $documents[] = $document = new \Model\Hashable();
            $document->setField('foo'.$i);
        }
        \Model\Hashable::getRepository()->save($documents);

        $this->assertSame($documents[3], \Model\Hashable::getRepository()->findByHash($documents[3]->getHash()));
        $this->assertSame($documents[6], \Model\Hashable::getRepository()->findByHash($documents[6]->getHash()));
    }

    public function testField()
    {
        $document = new \Model\HashableField();
        $document->setField('foo');
        $document->save();

        $this->assertNotNull($document->getAnotherField());
        $this->assertInternalType('string', $document->getAnotherField());
    }

    public function testLength()
    {
        $document = new \Model\HashableLength();
        $document->setField('foo');
        $document->save();

        $this->assertSame(5, strlen($document->getHash()));
    }
}
