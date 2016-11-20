<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Integration;

use GraphAware\Neo4j\OGM\Tests\Integration\Model\AuthUser;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Circle\NodeA;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Circle\NodeB;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Circle\NodeC;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Movie;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\User;
use LogicException;

/**
 *
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class CircularReferenceTest extends IntegrationTestCase
{
    /**
     * Create a fully connected graph
     */
    public function setUp()
    {
        parent::setUp();
        $this->clearDb();

        $nodeLimit = 10;
        $query = '';

        for ($i=0; $i<$nodeLimit;$i++) {
            $query .= sprintf('CREATE (a%d:NodeA {name:\'a%d\'})', $i, $i);
            $query .= sprintf('CREATE (b%d:NodeB {name:\'b%d\'})', $i, $i);
            $query .= sprintf('CREATE (c%d:NodeC {name:\'c%d\'})', $i, $i);
        }

        for ($i=0; $i<$nodeLimit;$i++) {
            for ($j = 0; $j < $nodeLimit; $j++) {
                $query .= sprintf('CREATE (a%d)-[:ALPHA]->(b%d)', $i, $j);
                $query .= sprintf('CREATE (b%d)-[:BETA]->(c%d)', $i, $j);
                $query .= sprintf('CREATE (c%d)-[:GAMMA]->(a%d)', $i, $j);
            }
        }

        $this->client->run($query);

    }

    public function testPersist()
    {
        /** @var NodeA $a */
        $a = $this->em->getRepository(NodeA::class)->findOneBy('name', 'a1');
        $a->setName('foobar');

        $this->em->flush();


    }
}
