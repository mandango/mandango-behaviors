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

class IpableTest extends TestCase
{
    public function testIpable()
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.0.1';

        $document = new \Model\Ipable();
        $document->setField('foo');
        $document->save();

        $this->assertSame('192.168.0.1', $document->getCreatedFrom());
        $this->assertNull($document->getUpdatedFrom());

        $_SERVER['REMOTE_ADDR'] = '192.168.0.100';

        $document->setField('bar');
        $document->save();

        $this->assertSame('192.168.0.100', $document->getUpdatedFrom());
        $this->assertSame('192.168.0.1', $document->getCreatedFrom());
    }
}
