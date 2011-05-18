<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\Tests\Behavior;

use Mandango\Tests\TestCase;

class TimestampableTest extends TestCase
{
    public function testTimestampable()
    {
        $document = $this->mandango->create('Model\Timestampable');
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
