<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Integration\Model\Circle;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * @OGM\Node(label="NodeB")
 */
class NodeB
{
    /**
     * @OGM\GraphId()
     */
    protected $id;

    /**
     * @OGM\Property(type="string")
     */
    protected $name;

    /**
     * @OGM\Relationship(targetEntity="NodeC", type="BETA", direction="OUTGOING", collection=true)
     */
    protected $cs;

    /**
     * @OGM\Relationship(targetEntity="NodeA", type="ALPHA", direction="INCOMING", collection=true)
     */
    protected $as;

    public function __construct($name)
    {
        $this->name = $name;
        $this->cs = new Collection();
        $this->as = new Collection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAs()
    {
        return $this->as;
    }

    public function addA(NodeA $node)
    {
        $this->as->add($node);
    }

    public function getCs()
    {
        return $this->cs;
    }

    public function addC(NodeC $node)
    {
        $this->cs->add($node);
    }
}
