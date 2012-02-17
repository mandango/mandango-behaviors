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

class SortableTest extends TestCase
{
    public function testDocumentIsFirst()
    {
        $documents = $this->createDocuments(3);

        $this->assertTrue($documents[1]->isFirst());
        $this->assertFalse($documents[2]->isFirst());
        $this->assertFalse($documents[3]->isFirst());
    }

    public function testDocumentIsFirstScope()
    {
        $documents = $this->createScopeDocuments(3);

        $this->assertTrue($documents['foo'][1]->isFirst());
        $this->assertFalse($documents['foo'][2]->isFirst());
        $this->assertFalse($documents['foo'][3]->isFirst());

        $this->assertTrue($documents['bar'][1]->isFirst());
        $this->assertFalse($documents['bar'][2]->isFirst());
        $this->assertFalse($documents['bar'][3]->isFirst());
    }

    public function testDocumentIsFirstInheritanceParent()
    {
        $documents = $this->createDocumentsInheritance(array('parent' => 2, 'child' => 1));

        $this->assertTrue($documents[1]->isFirst());
        $this->assertFalse($documents[2]->isFirst());
        $this->assertFalse($documents[3]->isFirst());
    }

    public function testDocumentIsFirstInheritanceChild()
    {
        $documents = $this->createDocumentsInheritance(array('child' => 2, 'parent' => 1));

        $this->assertTrue($documents[1]->isFirst());
        $this->assertFalse($documents[2]->isFirst());
        $this->assertFalse($documents[3]->isFirst());
    }

    public function testDocumentIsLast()
    {
        $documents = $this->createDocuments(3);

        $this->assertFalse($documents[1]->isLast());
        $this->assertFalse($documents[2]->isLast());
        $this->assertTrue($documents[3]->isLast());
    }

    public function testDocumentIsLastScope()
    {
        $documents = $this->createScopeDocuments(3);

        $this->assertFalse($documents['foo'][1]->isLast());
        $this->assertFalse($documents['foo'][2]->isLast());
        $this->assertTrue($documents['foo'][3]->isLast());

        $this->assertFalse($documents['bar'][1]->isLast());
        $this->assertFalse($documents['bar'][2]->isLast());
        $this->assertTrue($documents['bar'][3]->isLast());
    }

    public function testDocumentIsLastInheritanceParent()
    {
        $documents = $this->createDocumentsInheritance(array('child' => 1, 'parent' => 2));

        $this->assertFalse($documents[1]->isLast());
        $this->assertFalse($documents[2]->isLast());
        $this->assertTrue($documents[3]->isLast());
    }

    public function testDocumentIsLastInheritanceChild()
    {
        $documents = $this->createDocumentsInheritance(array('parent' => 1, 'child' => 1));


        $this->assertFalse($documents[1]->isLast());
        $this->assertTrue($documents[2]->isLast());
    }

    public function testDocumentGetPrevious()
    {
        $documents = $this->createDocuments(3);

        $this->assertNull($documents[1]->getPrevious());
        $this->assertSame($documents[1], $documents[2]->getPrevious());
        $this->assertSame($documents[2], $documents[3]->getPrevious());
    }

    public function testDocumentGetPreviousScope()
    {
        $documents = $this->createScopeDocuments(3);

        $this->assertNull($documents['foo'][1]->getPrevious());
        $this->assertSame($documents['foo'][1], $documents['foo'][2]->getPrevious());
        $this->assertSame($documents['foo'][2], $documents['foo'][3]->getPrevious());

        $this->assertNull($documents['bar'][1]->getPrevious());
        $this->assertSame($documents['bar'][1], $documents['bar'][2]->getPrevious());
        $this->assertSame($documents['bar'][2], $documents['bar'][3]->getPrevious());
    }

    public function testDocumentGetPreviousInheritance()
    {
        $documents = $this->createDocumentsInheritance(array('parent' => 2, 'child' => 2));

        $this->assertNull($documents[1]->getPrevious());
        $this->assertEquals($documents[1]->getId(), $documents[2]->getPrevious()->getId());
        $this->assertEquals($documents[2]->getId(), $documents[3]->getPrevious()->getId());
        $this->assertEquals($documents[3]->getId(), $documents[4]->getPrevious()->getId());
    }

    public function testDocumentGetNext()
    {
        $documents = $this->createDocuments(3);

        $this->assertSame($documents[2], $documents[1]->getNext());
        $this->assertSame($documents[3], $documents[2]->getNext());
        $this->assertNull($documents[3]->getNext());
    }

    public function testDocumentGetNextScope()
    {
        $documents = $this->createScopeDocuments(3);

        $this->assertSame($documents['foo'][2], $documents['foo'][1]->getNext());
        $this->assertSame($documents['foo'][3], $documents['foo'][2]->getNext());
        $this->assertNull($documents['foo'][3]->getNext());

        $this->assertSame($documents['bar'][2], $documents['bar'][1]->getNext());
        $this->assertSame($documents['bar'][3], $documents['bar'][2]->getNext());
        $this->assertNull($documents['bar'][3]->getNext());
    }

    public function testDocumentGetNextInheritance()
    {
        $documents = $this->createDocumentsInheritance(array('child' => 2, 'parent' => 2));

        $this->assertEquals($documents[2]->getId(), $documents[1]->getNext()->getId());
        $this->assertEquals($documents[3]->getId(), $documents[2]->getNext()->getId());
        $this->assertEquals($documents[4]->getId(), $documents[3]->getNext()->getId());
        $this->assertNull($documents[4]->getNext());
    }

    public function testDocumentSwapPosition()
    {
        $documents = $this->createDocuments(5);
        $documents[2]->swapPosition($documents[4]);

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[4]->getPosition());
        $this->assertSame(3, $documents[3]->getPosition());
        $this->assertSame(4, $documents[2]->getPosition());
        $this->assertSame(5, $documents[5]->getPosition());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDocumentSwapPositionDifferentClass()
    {
        $sortable = $this->mandango->create('Model\Sortable');
        $hashable = $this->mandango->create('Model\Hashable');

        $sortable->swapPosition($hashable);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentSwapPositionThisNew()
    {
        $documents = $this->createDocuments(3);

        $this->mandango->create('Model\Sortable')
            ->setName('foo')
            ->swapPosition($documents[2])
        ;
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentSwapPositionOtherNew()
    {
        $documents = $this->createDocuments(3);

        $document = $this->mandango->create('Model\Sortable')->setName('foo');
        $documents[2]->swapPosition($document);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentSwapPositionThisModified()
    {
        $documents = $this->createDocuments(3);
        $documents[2]->setName('foo')->swapPosition($documents[1]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentSwapPositionOtherModified()
    {
        $documents = $this->createDocuments(3);
        $documents[2]->swapPosition($documents[1]->setName('foo'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentSwapPositionDifferentScope()
    {
        $documents = $this->createScopeDocuments(3);
        $documents['foo'][1]->swapPosition($documents['bar'][2]);
    }

    public function testDocumentMoveUp()
    {
        $documents = $this->createDocuments(5);
        $documents[3]->moveUp();

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[3]->getPosition());
        $this->assertSame(3, $documents[2]->getPosition());
        $this->assertSame(4, $documents[4]->getPosition());
        $this->assertSame(5, $documents[5]->getPosition());
    }

    public function testDocumentMoveUpScope()
    {
        $documents = $this->createScopeDocuments(5);
        $documents['foo'][3]->moveUp();

        $this->assertSame(1, $documents['foo'][1]->getPosition());
        $this->assertSame(2, $documents['foo'][3]->getPosition());
        $this->assertSame(3, $documents['foo'][2]->getPosition());
        $this->assertSame(4, $documents['foo'][4]->getPosition());
        $this->assertSame(5, $documents['foo'][5]->getPosition());

        $this->assertSame(1, $documents['bar'][1]->getPosition());
        $this->assertSame(2, $documents['bar'][2]->getPosition());
        $this->assertSame(3, $documents['bar'][3]->getPosition());
        $this->assertSame(4, $documents['bar'][4]->getPosition());
        $this->assertSame(5, $documents['bar'][5]->getPosition());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentMoveUpFirst()
    {
        $documents = $this->createDocuments(5);
        $documents[1]->moveUp();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentMoveUpFirstScope1()
    {
        $documents = $this->createScopeDocuments(3);
        $documents['foo'][1]->moveUp();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentMoveUpFirstScope2()
    {
        $documents = $this->createScopeDocuments(3);
        $documents['bar'][1]->moveUp();
    }

    public function testDocumentMoveDown()
    {
        $documents = $this->createDocuments(5);
        $documents[3]->moveDown();

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[2]->getPosition());
        $this->assertSame(3, $documents[4]->getPosition());
        $this->assertSame(4, $documents[3]->getPosition());
        $this->assertSame(5, $documents[5]->getPosition());
    }

    public function testDocumentMoveDownScope()
    {
        $documents = $this->createScopeDocuments(5);
        $documents['foo'][3]->moveDown();

        $this->assertSame(1, $documents['foo'][1]->getPosition());
        $this->assertSame(2, $documents['foo'][2]->getPosition());
        $this->assertSame(3, $documents['foo'][4]->getPosition());
        $this->assertSame(4, $documents['foo'][3]->getPosition());
        $this->assertSame(5, $documents['foo'][5]->getPosition());

        $this->assertSame(1, $documents['bar'][1]->getPosition());
        $this->assertSame(2, $documents['bar'][2]->getPosition());
        $this->assertSame(3, $documents['bar'][3]->getPosition());
        $this->assertSame(4, $documents['bar'][4]->getPosition());
        $this->assertSame(5, $documents['bar'][5]->getPosition());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentMoveDownLast()
    {
        $documents = $this->createDocuments(5);
        $documents[5]->moveDown();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentMoveDownLastScope1()
    {
        $documents = $this->createScopeDocuments(5);
        $documents['foo'][5]->moveDown();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentMoveDownLastScope2()
    {
        $documents = $this->createScopeDocuments(5);
        $documents['bar'][5]->moveDown();
    }

    public function testDocumentSortableSetPosition()
    {
        $documents = $this->createDocuments(5);

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[2]->getPosition());
        $this->assertSame(3, $documents[3]->getPosition());
        $this->assertSame(4, $documents[4]->getPosition());
        $this->assertSame(5, $documents[5]->getPosition());
    }

    public function testDocumentSortableSetPositionSameStringPosition()
    {
        $documents = $this->createDocuments(5);
        $documents[2]->setPosition('2')->save();

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[2]->getPosition());
        $this->assertSame(3, $documents[3]->getPosition());
        $this->assertSame(4, $documents[4]->getPosition());
        $this->assertSame(5, $documents[5]->getPosition());
    }

    /**
     * @expectedException \RuntimeException
     * @dataProvider documentSortableSetPositionLowerThanMinProvider
     */
    public function testDocumentSortableSetPositionLowerThanMin($position)
    {
        $documents = $this->createDocuments(3);
        $this->mandango->create('Model\Sortable')->setName('foo')->setPosition($position)->save();
    }

    public function documentSortableSetPositionLowerThanMinProvider()
    {
        return array(
            array(0),
            array(-1),
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDocumentSortableSetPositionHigherThanMax()
    {
        $documents = $this->createDocuments(3);
        $this->mandango->create('Model\Sortable')->setName('foo')->setPosition(5)->save();
    }

    public function testDocumentSortableSetPositionScope()
    {
        $documents = $this->createScopeDocuments(5);

        foreach ($documents as $docs) {
            foreach ($docs as $doc) {
                $doc->refresh();
            }
        }

        $this->assertSame(1, $documents['foo'][1]->getPosition());
        $this->assertSame(2, $documents['foo'][2]->getPosition());
        $this->assertSame(3, $documents['foo'][3]->getPosition());
        $this->assertSame(4, $documents['foo'][4]->getPosition());
        $this->assertSame(5, $documents['foo'][5]->getPosition());

        $this->assertSame(1, $documents['bar'][1]->getPosition());
        $this->assertSame(2, $documents['bar'][2]->getPosition());
        $this->assertSame(3, $documents['bar'][3]->getPosition());
        $this->assertSame(4, $documents['bar'][4]->getPosition());
        $this->assertSame(5, $documents['bar'][5]->getPosition());
    }

    public function testDocumentSortableSetPositionScopeReference()
    {
        $sortable1 = $this->mandango->create('Model\Sortable')->setName('foo')->save();
        $sortable2 = $this->mandango->create('Model\Sortable')->setName('bar')->save();

        $documents = array();
        for ($i = 1; $i <= 3; $i++) {
            $documents[$i] = $this->mandango->create('Model\SortableScopeReference')
                ->setName('foo')
                ->setSortable($sortable1)
                ->save()
            ;
        }
        for ($i = 4; $i <= 5; $i++) {
            $documents[$i] = $this->mandango->create('Model\SortableScopeReference')
                ->setName('foo')
                ->setSortable($sortable2)
                ->save()
            ;
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[2]->getPosition());
        $this->assertSame(3, $documents[3]->getPosition());
        $this->assertSame(1, $documents[4]->getPosition());
        $this->assertSame(2, $documents[5]->getPosition());
    }

    public function testDocumentSortableSetPositionSkip()
    {
        $documents = array();
        for ($i = 1; $i <= 5; $i++) {
            $documents[$i] = $this->mandango->create('Model\SortableSkip')
                ->setName('foo')
                ->setSkip($i % 2 ? false : true)
                ->save()
            ;
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertNull($documents[2]->getPosition());
        $this->assertSame(2, $documents[3]->getPosition());
        $this->assertNull($documents[4]->getPosition());
        $this->assertSame(3, $documents[5]->getPosition());
    }

    public function testDocumentSortableInheritance()
    {
        $documents = $this->createDocumentsInheritance(array('parent' => 3, 'child' => 2));

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[2]->getPosition());
        $this->assertSame(3, $documents[3]->getPosition());
        $this->assertSame(4, $documents[4]->getPosition());
        $this->assertSame(5, $documents[5]->getPosition());
    }

    public function testDocumentSortableSetPositionTop()
    {
        $documents = $this->createDocuments(5, true);

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(5, $documents[1]->getPosition());
        $this->assertSame(4, $documents[2]->getPosition());
        $this->assertSame(3, $documents[3]->getPosition());
        $this->assertSame(2, $documents[4]->getPosition());
        $this->assertSame(1, $documents[5]->getPosition());
    }

    public function testDocumentSortableSetPositionMoveDocumentsNew()
    {
        $documents = $this->createDocuments(5);
        $documents[6] = $this->mandango->create('Model\Sortable')
            ->setName('foo')
            ->setPosition(3)
            ->save()
        ;

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[2]->getPosition());
        $this->assertSame(3, $documents[6]->getPosition());
        $this->assertSame(4, $documents[3]->getPosition());
        $this->assertSame(5, $documents[4]->getPosition());
        $this->assertSame(6, $documents[5]->getPosition());
    }

    public function testDocumentSortableSetPositionMoveDocumentsNewScope()
    {
        $documents = $this->createScopeDocuments(5);
        $documents['foo'][6] = $this->mandango->create('Model\SortableScope')
            ->setType('foo')
            ->setName('ups')
            ->setPosition(3)
            ->save()
        ;

        foreach ($documents as $docs) {
            foreach ($docs as $doc) {
                $doc->refresh();
            }
        }

        $this->assertSame(1, $documents['foo'][1]->getPosition());
        $this->assertSame(2, $documents['foo'][2]->getPosition());
        $this->assertSame(3, $documents['foo'][6]->getPosition());
        $this->assertSame(4, $documents['foo'][3]->getPosition());
        $this->assertSame(5, $documents['foo'][4]->getPosition());
        $this->assertSame(6, $documents['foo'][5]->getPosition());

        $this->assertSame(1, $documents['bar'][1]->getPosition());
        $this->assertSame(2, $documents['bar'][2]->getPosition());
        $this->assertSame(3, $documents['bar'][3]->getPosition());
        $this->assertSame(4, $documents['bar'][4]->getPosition());
        $this->assertSame(5, $documents['bar'][5]->getPosition());
    }

    public function testDocumentSortableSetPositionMoveDocumentsNewInheritance()
    {
        $documents = $this->createDocumentsInheritance(array('child' => 3, 'parent' => 2));
        $documents[6] = $this->mandango->create('Model\SortableChild')
            ->setName('foo')
            ->setPosition(3)
            ->save()
        ;

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[2]->getPosition());
        $this->assertSame(3, $documents[6]->getPosition());
        $this->assertSame(4, $documents[3]->getPosition());
        $this->assertSame(5, $documents[4]->getPosition());
        $this->assertSame(6, $documents[5]->getPosition());
    }

    public function testDocumentSortableSetPositionEditPositionUp()
    {
        $documents = $this->createDocuments(5);
        $documents[4]->setPosition(2)->save();

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[4]->getPosition());
        $this->assertSame(3, $documents[2]->getPosition());
        $this->assertSame(4, $documents[3]->getPosition());
        $this->assertSame(5, $documents[5]->getPosition());
    }

    public function testDocumentSortableSetPositionEditPositionUpScope()
    {
        $documents = $this->createScopeDocuments(5);
        $documents['foo'][4]->setPosition(2)->save();

        foreach ($documents as $docs) {
            foreach ($docs as $doc) {
                $doc->refresh();
            }
        }

        $this->assertSame(1, $documents['foo'][1]->getPosition());
        $this->assertSame(2, $documents['foo'][4]->getPosition());
        $this->assertSame(3, $documents['foo'][2]->getPosition());
        $this->assertSame(4, $documents['foo'][3]->getPosition());
        $this->assertSame(5, $documents['foo'][5]->getPosition());

        $this->assertSame(1, $documents['bar'][1]->getPosition());
        $this->assertSame(2, $documents['bar'][2]->getPosition());
        $this->assertSame(3, $documents['bar'][3]->getPosition());
        $this->assertSame(4, $documents['bar'][4]->getPosition());
        $this->assertSame(5, $documents['bar'][5]->getPosition());
    }

    public function testDocumentSortableSetPositionEditPositionUpInheritance()
    {
        $documents = $this->createDocumentsInheritance(array('parent' => 3, 'child' => 2));
        $documents[4]->setPosition(2)->save();

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[4]->getPosition());
        $this->assertSame(3, $documents[2]->getPosition());
        $this->assertSame(4, $documents[3]->getPosition());
        $this->assertSame(5, $documents[5]->getPosition());
    }

    public function testDocumentSortableSetPositionEditPositionDown()
    {
        $documents = $this->createDocuments(5);
        $documents[2]->setPosition(4)->save();

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[3]->getPosition());
        $this->assertSame(3, $documents[4]->getPosition());
        $this->assertSame(4, $documents[2]->getPosition());
        $this->assertSame(5, $documents[5]->getPosition());
    }

    public function testDocumentSortableSetPositionEditPositionDownScope()
    {
        $documents = $this->createScopeDocuments(5);
        $documents['foo'][2]->setPosition(4)->save();

        foreach ($documents as $docs) {
            foreach ($docs as $doc) {
                $doc->refresh();
            }
        }

        $this->assertSame(1, $documents['foo'][1]->getPosition());
        $this->assertSame(2, $documents['foo'][3]->getPosition());
        $this->assertSame(3, $documents['foo'][4]->getPosition());
        $this->assertSame(4, $documents['foo'][2]->getPosition());
        $this->assertSame(5, $documents['foo'][5]->getPosition());

        $this->assertSame(1, $documents['bar'][1]->getPosition());
        $this->assertSame(2, $documents['bar'][2]->getPosition());
        $this->assertSame(3, $documents['bar'][3]->getPosition());
        $this->assertSame(4, $documents['bar'][4]->getPosition());
        $this->assertSame(5, $documents['bar'][5]->getPosition());
    }

    public function testDocumentSortableSetPositionEditPositionDownInheritance()
    {
        $documents = $this->createDocumentsInheritance(array('child' => 3, 'parent' => 2));
        $documents[2]->setPosition(4)->save();

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[3]->getPosition());
        $this->assertSame(3, $documents[4]->getPosition());
        $this->assertSame(4, $documents[2]->getPosition());
        $this->assertSame(5, $documents[5]->getPosition());
    }

    public function testDocumentSortableRemovePosition()
    {
        $documents = $this->createDocuments(5);
        $documents[3]->delete();
        unset($documents[3]);

        foreach ($documents as $document) {
            $document->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[2]->getPosition());
        $this->assertSame(3, $documents[4]->getPosition());
        $this->assertSame(4, $documents[5]->getPosition());
    }

    public function testDocumentSortableRemovePositionScope()
    {
        $documents = $this->createScopeDocuments(5);
        $documents['foo'][3]->delete();
        unset($documents['foo'][3]);

        foreach ($documents as $docs) {
            foreach ($docs as $doc) {
                $doc->refresh();
            }
        }

        $this->assertSame(1, $documents['foo'][1]->getPosition());
        $this->assertSame(2, $documents['foo'][2]->getPosition());
        $this->assertSame(3, $documents['foo'][4]->getPosition());
        $this->assertSame(4, $documents['foo'][5]->getPosition());

        $this->assertSame(1, $documents['bar'][1]->getPosition());
        $this->assertSame(2, $documents['bar'][2]->getPosition());
        $this->assertSame(3, $documents['bar'][3]->getPosition());
        $this->assertSame(4, $documents['bar'][4]->getPosition());
        $this->assertSame(5, $documents['bar'][5]->getPosition());
    }

    public function testDocumentSortableRemovePositionSkip()
    {
        $documents = array();
        for ($i = 1; $i <= 5; $i++) {
            $documents[$i] = $this->mandango->create('Model\SortableSkip')
                ->setName('foo')
                ->setSkip($i % 2 ? false : true)
                ->save()
            ;
        }
        $documents[3]->delete();
        unset($documents[3]);

        foreach ($documents as $doc) {
            $doc->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertNull($documents[2]->getPosition());
        $this->assertNull($documents[4]->getPosition());
        $this->assertSame(2, $documents[5]->getPosition());
    }

    public function testDocumentSortableRemovePositionInheritance()
    {
        $documents = $this->createDocumentsInheritance(array('child' => 3, 'parent' => 2));
        $documents[2]->delete();
        unset($documents[2]);

        foreach ($documents as $doc) {
            $doc->refresh();
        }

        $this->assertSame(1, $documents[1]->getPosition());
        $this->assertSame(2, $documents[3]->getPosition());
        $this->assertSame(3, $documents[4]->getPosition());
        $this->assertSame(4, $documents[5]->getPosition());
    }

    public function testRepositoryGetMinPosition()
    {
        $documents = $this->createDocuments(5);
        $this->assertSame(1, $this->mandango->getRepository('Model\Sortable')->getMinPosition());
    }

    public function testRepositoryGetMinPositionScope()
    {
        $documents = $this->createScopeDocuments(5);
        $repository = $this->mandango->getRepository('Model\SortableScope');
        $this->assertSame(1, $repository->getMinPosition(array('type' => 'foo')));
        $this->assertSame(1, $repository->getMinPosition(array('type' => 'bar')));
        $this->assertNull($repository->getMinPosition(array('type' => 'ups')));
    }

    public function testRepositoryGetMaxPosition()
    {
        $documents = $this->createDocuments(5);
        $this->assertSame(5, $this->mandango->getRepository('Model\Sortable')->getMaxPosition());
    }

    public function testRepositoryGetMaxPositionScope()
    {
        $documents = $this->createScopeDocuments(5);
        $documents['foo'][5]->delete();

        $repository = $this->mandango->getRepository('Model\SortableScope');
        $this->assertSame(4, $repository->getMaxPosition(array('type' => 'foo')));
        $this->assertSame(5, $repository->getMaxPosition(array('type' => 'bar')));
    }

    private function createDocuments($nb, $top = false)
    {
        $documents = array();
        for ($i = 1; $i <= $nb; $i++) {
            $documents[$i] = $this->mandango->create($top ? 'Model\SortableTop' : 'Model\Sortable')
                ->setName('sortable'.$i)
            ;
            $this->mandango->persist($documents[$i]);
        }
        $this->mandango->flush();

        return $documents;
    }

    private function createScopeDocuments($nb, array $scopes = array('foo', 'bar'))
    {
        $documents = array();
        foreach ($scopes as $type) {
            $documents[$type] = array();
            for ($i = 1; $i <= $nb; $i++) {
                $documents[$type][$i] = $this->mandango->create('Model\SortableScope')
                    ->setType($type)
                    ->setName('sortable'.$i)
                ;
                $this->mandango->persist($documents[$type][$i]);
            }
        }
        $this->mandango->flush();

        return $documents;
    }

    private function createDocumentsInheritance(array $what)
    {
        $documents = array();
        $idx = 0;
        foreach ($what as $type => $nb) {
            if ('parent' === $type) {
                for ($i = 0; $i < $nb; $i++) {
                    $documents[++$idx] = $this->mandango->create('Model\SortableParent')->setName('foo')->save();
                }
            } elseif ('child' === $type) {
                for ($i = 0; $i < $nb; $i++) {
                    $documents[++$idx] = $this->mandango->create('Model\SortableChild')->setName('foo')->save();
                }
            }
        }

        return $documents;
    }
}
