<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

/**
 * Class GithubUser
 * @package GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model
 *
 * @OGM\Node(label="User")
 */
class GithubUser
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    private $id;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    private $login;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    private $description;

    /**
     * @var GithubRepository[]|Collection
     *
     * @OGM\Relationship(targetEntity="GithubRepository", type="OWNS", direction="OUTGOING", collection=true, mappedBy="owner")
     * @OGM\Lazy()
     */
    private $ownedRepositories;

    /**
     * @var GithubRepository[]|Collection
     *
     * @OGM\Relationship(targetEntity="GithubRepository", type="STARS", direction="OUTGOING", collection=true, mappedBy="stargazers")
     * @OGM\Lazy()
     */
    private $starred;

    /**
     * @var GithubUser[]|Collection
     *
     * @OGM\Relationship(targetEntity="GithubUser", type="FOLLOWS", direction="OUTGOING", collection=true, mappedBy="followedBy")
     * @OGM\Lazy()
     */
    private $follows;

    /**
     * @var GithubUser[]|Collection
     *
     * @OGM\Relationship(targetEntity="GithubUser", type="FOLLOWS", direction="OUTGOING", collection=true, mappedBy="follows")
     * @OGM\Lazy()
     */
    private $followedBy;

    /**
     * @var Organization[]
     *
     * @OGM\Relationship(targetEntity="Organization", type="MEMBER_OF", direction="OUTGOING", mappedBy="members", collection=true)
     */
    private $organizations;

    public function __construct($login)
    {
        $this->login = $login;
        $this->ownedRepositories = new Collection();
        $this->starred = new Collection();
        $this->follows = new Collection();
        $this->followedBy = new Collection();
        $this->organizations = new Collection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Common\Collection|\GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubRepository[]
     */
    public function getOwnedRepositories()
    {
        return $this->ownedRepositories;
    }

    public function removeRepository(GithubRepository $repository)
    {
        if ($repository->getOwner()->getId() !== $this->getId()) {
            return;
        }

        foreach ($this->ownedRepositories as $ownedRepository) {
            if ($ownedRepository->getId() === $repository->getId()) {
                $this->ownedRepositories->removeElement($ownedRepository);
            }
        }
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Common\Collection|\GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubRepository[]
     */
    public function getStarred()
    {
        return $this->starred;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Common\Collection|\GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubUser[]
     */
    public function getFollows()
    {
        return $this->follows;
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Common\Collection|\GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubUser[]
     */
    public function getFollowedBy()
    {
        return $this->followedBy;
    }

    /**
     * @param \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\Organization $organization
     */
    public function addOrganization(Organization $organization)
    {
        $this->organizations->add($organization);
    }

    /**
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\Organization[]
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }
}