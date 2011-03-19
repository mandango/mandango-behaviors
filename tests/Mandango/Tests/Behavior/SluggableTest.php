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

class SluggableTest extends TestCase
{
    public function testSluggable()
    {
        $documents = array();

        $documents[1] = new \Model\Sluggable();
        $documents[1]->setTitle(' Testing Sluggable Extensión ');
        $documents[1]->save();

        $this->assertSame('testing-sluggable-extension', $documents[1]->getSlug());

        $documents[2] = new \Model\Sluggable();
        $documents[2]->setTitle(' Testing Sluggable Extensión ');
        $documents[2]->save();

        $this->assertSame('testing-sluggable-extension-2', $documents[2]->getSlug());
    }

    public function testRepositoryFindBySlug()
    {
        $documents = array();
        for ($i = 0; $i < 9; $i++) {
            $documents[$i] = $document = new \Model\Sluggable();
            $document->setTitle('foo');
            $document->save();
        }

        $this->assertSame($documents[3], \Model\Sluggable::repository()->findBySlug($documents[3]->getSlug()));
        $this->assertSame($documents[6], \Model\Sluggable::repository()->findBySlug($documents[6]->getSlug()));
    }
}
