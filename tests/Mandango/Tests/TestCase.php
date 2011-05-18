<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\Tests;

use Mandango\Cache\ArrayCache;
use Mandango\Connection;
use Mandango\Mandango;
use Mandango\Archive;
use Mandango\Type\Container as TypeContainer;

class TestCase extends \PHPUnit_Framework_TestCase
{
    static protected $staticConnection;
    static protected $staticMandango;

    protected $metadataFactoryClass = 'Model\Mapping\MetadataFactory';
    protected $server = 'mongodb://localhost:27017';
    protected $dbName = 'mandango_behaviors_tests';

    protected $connection;
    protected $mandango;
    protected $unitOfWork;
    protected $metadataFactory;
    protected $cache;
    protected $mongo;
    protected $db;

    protected function setUp()
    {
        if (!static::$staticConnection) {
            static::$staticConnection = new Connection($this->server, $this->dbName);
        }
        $this->connection = static::$staticConnection;

        if (!static::$staticMandango) {
            static::$staticMandango = new Mandango(new $this->metadataFactoryClass, new ArrayCache(), function($log) {});
            static::$staticMandango->setConnection('default', $this->connection);
            static::$staticMandango->setDefaultConnectionName('default');
        }
        $this->mandango = static::$staticMandango;
        $this->unitOfWork = $this->mandango->getUnitOfWork();
        $this->metadataFactory = $this->mandango->getMetadataFactory();
        $this->cache = $this->mandango->getCache();

        foreach ($this->mandango->getAllRepositories() as $repository) {
            $repository->getIdentityMap()->clear();
        }

        $this->mongo = $this->connection->getMongo();
        $this->db = $this->connection->getMongoDB();

        foreach ($this->db->listCollections() as $collection) {
            $collection->drop();
        }
    }

    protected function tearDown()
    {
        Archive::clear();
        TypeContainer::reset();
    }
}
