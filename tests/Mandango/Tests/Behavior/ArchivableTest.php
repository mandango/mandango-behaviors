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

class ArchivableTest extends TestCase
{
    public function testArchivable()
    {
        $model = $this->mandango->create('Model\Archivable')
            ->setTitle('foo')
            ->save();

        $archive = $model->archive();

        $this->assertInstanceOf('Model\ArchivableArchive', $archive);
        $this->assertFalse($archive->isNew());
        $this->assertEquals($model->getId(), $archive->getDocumentId());
        $this->assertNotNull($archive->getArchivedAt());
        $this->assertSame('foo', $archive->getTitle());

        $repository = $model->getRepository();
        $archiveRepository = $this->mandango->getRepository('Model\ArchivableArchive');

        $this->assertSame(1, $repository->count());
        $this->assertSame(1, $archiveRepository->count());
    }

    public function testArchivableReference()
    {
        $article = $this->mandango->create('Model\Article')
            ->setTitle('foo')
            ->save();

        $model = $this->mandango->create('Model\ArchivableReference')
            ->setArticle($article)
            ->save();

        $archive = $model->archive();

        $repository = $model->getRepository();
        $archiveRepository = $this->mandango->getRepository('Model\ArchivableReferenceArchive');

        $this->assertSame(1, $repository->count());
        $this->assertSame(1, $archiveRepository->count());
    }

    public function testArchiveInsert()
    {
        $model = $this->mandango->create('Model\ArchivableInsert')
            ->setTitle('foo')
            ->save();

        $repository = $model->getRepository();
        $archiveRepository = $this->mandango->getRepository('Model\ArchivableInsertArchive');

        $this->assertSame(1, $repository->count());
        $this->assertSame(1, $archiveRepository->count());

        $archive = $archiveRepository->createQuery()->one();

        $this->assertSame('foo', $archive->getTitle());
    }

    public function testArchiveUpdate()
    {
        $model = $this->mandango->create('Model\ArchivableUpdate')
            ->setTitle('foo')
            ->save();

        $repository = $model->getRepository();
        $archiveRepository = $this->mandango->getRepository('Model\ArchivableUpdateArchive');

        $this->assertSame(1, $repository->count());
        $this->assertSame(0, $archiveRepository->count());

        $model->setTitle('bar')->save();

        $this->assertSame(1, $repository->count());
        $this->assertSame(1, $archiveRepository->count());

        $archive = $archiveRepository->createQuery()->one();

        $this->assertSame('bar', $archive->getTitle());
    }

    public function testArchiveDelete()
    {
        $model = $this->mandango->create('Model\ArchivableDelete')
            ->setTitle('foo')
            ->save();

        $repository = $model->getRepository();
        $archiveRepository = $this->mandango->getRepository('Model\ArchivableDeleteArchive');

        $this->assertSame(1, $repository->count());
        $this->assertSame(0, $archiveRepository->count());

        $model->delete();

        $this->assertSame(0, $repository->count());
        $this->assertSame(1, $archiveRepository->count());

        $archive = $archiveRepository->createQuery()->one();

        $this->assertSame('foo', $archive->getTitle());
    }
}