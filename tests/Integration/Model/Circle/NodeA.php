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
 * @OGM\Node(label="NodeA")
 */
class NodeA
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
     * @OGM\Relationship(targetEntity="NodeB", type="ALPHA", direction="OUTGOING", collection=true)
     */
    protected $bs;

    /**
     * @OGM\Relationship(targetEntity="NodeC", type="GAMMA", direction="INCOMING", collection=true)
     */
    protected $cs;

    public function __construct($name)
    {
        $this->name = $name;
        $this->bs = new Collection();
        $this->cs = new Collection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return NodeA
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getBs()
    {
        return $this->bs;
    }

    public function addB(NodeB $node)
    {
        $this->bs->add($node);
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
