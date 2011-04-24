<?php

/*
 * Copyright 2010 Pablo Díez <pablodip@gmail.com>
 *
 * This file is part of Mandango.
 *
 * Mandango is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Mandango is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Mandango. If not, see <http://www.gnu.org/licenses/>.
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
