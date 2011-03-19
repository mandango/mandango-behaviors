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

namespace Mandango\Tests\Behavior;

use Mandango\Tests\TestCase;

class TimestampableTest extends TestCase
{
    public function testTimestampable()
    {
        $document = new \Model\Timestampable();
        $document->setField('foo');
        $document->save();

        $this->assertEquals(new \DateTime(), $createdAt = $document->getCreatedAt());
        $this->assertNull($document->getUpdatedAt());

        $document->setField('bar');
        $document->save();

        $this->assertEquals(new \DateTime(), $updatedAt = $document->getUpdatedAt());
        $this->assertSame($createdAt, $document->getCreatedAt());
        $this->assertNotSame($updatedAt, $createdAt);
    }
}
