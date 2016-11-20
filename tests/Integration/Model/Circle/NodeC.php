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
 * @OGM\Node(label="NodeC")
 */
class NodeC
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
     * @OGM\Relationship(targetEntity="NodeA", type="GAMMA", direction="OUTGOING", collection=true)
     */
    protected $as;

    /**
     * @OGM\Relationship(targetEntity="NodeB", type="BETA", direction="INCOMING", collection=true)
     */
    protected $bs;

    public function __construct($name)
    {
        $this->name = $name;
        $this->as = new Collection();
        $this->bs = new Collection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getBs()
    {
        return $this->bs;
    }

    public function addB(NodeB $node)
    {
        $this->bs->add($node);
    }

    public function getAs()
    {
        return $this->as;
    }

    public function addA(NodeA $node)
    {
        $this->as->add($node);
    }
}
