<?php

namespace GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github;

use GraphAware\Neo4j\OGM\Lazy\LazyRelationshipCollection;
use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubRepository;
use GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubUser;
use GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\Language;
use GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\Organization;

class GithubIntegrationTest extends IntegrationTestCase
{
    public function testSimpleUserIsSaved()
    {
        $this->clearDb();
        $user = new GithubUser('ikwattro');
        $user->setDescription("neo4j consultant");
        $this->em->persist($user);
        $this->em->flush();

        $this->assertGraphExist('(u:User {login:"ikwattro", description:"neo4j consultant"})');
    }

    public function testSimpleUserIsFetched()
    {
        $this->clearDb();
        $this->client->run("CREATE (n:User {login:'ikwattro'})-[:MEMBER_OF]->(o:Organization {name:'GraphAware'})-[:IN_COUNTRY]->(c:Country {name:'UK'}),
        (n2:User {login:'alenegro81'})
        MERGE (n2)-[:MEMBER_OF]->(o)");
        /** @var GithubUser $user */
        $user = $this->em->getRepository(GithubUser::class)->findOneBy('login', 'ikwattro');
        $this->assertCount(1, $user->getOrganizations());
        $this->assertEquals("ikwattro", $user->getOrganizations()[0]->getMembers()[0]->getLogin());
        $this->assertEquals("ikwattro", $user->getOrganizations()[0]->getMembers()[0]->getOrganizations()[0]->getMembers()[0]->getLogin());
        $this->assertInstanceOf(LazyRelationshipCollection::class, $user->getOwnedRepositories());
    }

    public function testSimpleUserIsSavedAndUpdatedPropertiesAreUpdated()
    {
        $this->clearDb();
        $user = new GithubUser('ikwattro');
        $user->setDescription("neo4j consultant");
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro", description:"neo4j consultant"})');
        $user->setDescription("neo4j developer");
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro", description:"neo4j developer"})');
    }

    public function testUserIsFetchedFromDatabaseAndUpdated()
    {
        $this->clearDb();
        $user = new GithubUser('ikwattro');
        $user->setDescription("neo4j consultant");
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro", description:"neo4j consultant"})');
        $this->em->clear();

        /** @var GithubUser $ikwattro */
        $ikwattro = $this->em->getRepository(GithubUser::class)->findOneBy('login', 'ikwattro');
        $this->assertEquals('ikwattro', $ikwattro->getLogin());
        $this->assertEquals('neo4j consultant', $ikwattro->getDescription());
        $this->assertTrue($ikwattro->getOwnedRepositories() instanceof LazyRelationshipCollection);
        $ikwattro->setDescription("neo4j developer");
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro", description:"neo4j developer"})');
    }

    public function testUserAssignedRepositoryWorkflow()
    {
        $this->clearDb();
        $user = $this->createUser('ikwattro');
        $repo = new GithubRepository('neo4j-reco', $user);
        $user->getOwnedRepositories()->add($repo);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"ikwattro"})-[:OWNS]->(r:Repository {name:"neo4j-reco"})');
        $this->em->clear();

        /** @var GithubUser $ikwattro */
        $ikwattro = $this->em->getRepository(GithubUser::class)->findOneBy('login', 'ikwattro');
        $this->assertTrue($ikwattro->getOwnedRepositories()->first() instanceof GithubRepository);
        $this->assertInstanceOf(LazyRelationshipCollection::class, $ikwattro->getOwnedRepositories());
        $this->assertEquals($ikwattro->getLogin(), $ikwattro->getOwnedRepositories()[0]->getOwner()->getLogin());
        $repo = $ikwattro->getOwnedRepositories()[0];
        $ikwattro->removeRepository($repo);
        $this->assertCount(0, $ikwattro->getOwnedRepositories());
        $this->em->flush();
        $this->assertGraphNotExist('(u:User {login:"ikwattro"})-[:OWNS]->(r:Repository {name:"neo4j-reco"})');
        // test nodes are not changed
        $this->assertGraphExist('(u:User {login:"ikwattro"}), (r:Repository {name:"neo4j-reco"})');
    }

    public function testFetchingFromOrganizationAndUpdates()
    {
        $this->clearDb();
        $this->clearDb();
        $this->client->run("CREATE (n:User {login:'ikwattro'})-[:MEMBER_OF]->(o:Organization {name:'GraphAware'})-[:IN_COUNTRY]->(c:Country {name:'UK'}),
        (n2:User {login:'alenegro81'})
        MERGE (n2)-[:MEMBER_OF]->(o)");

        /** @var Organization $org */
        $org = $this->em->getRepository(Organization::class)->findOneBy('name', 'GraphAware');
        $this->assertInstanceOf(LazyRelationshipCollection::class, $org->getMembers());
        $this->assertCount(2, $org->getMembers());
        $this->assertEquals('UK', $org->getCountry()->getName());
        $this->assertEquals('ikwattro', $org->getMember('ikwattro')->getOrganizations()[0]->getMember('ikwattro')->getOrganizations()[0]->getMember('ikwattro')->getLogin());
        $neo = new Organization("neo4j");
        $org->getMember('ikwattro')->addOrganization($neo);
        $this->em->flush();
        $this->assertGraphExist('(o:Organization {name:"GraphAware"})<-[:MEMBER_OF]-(u:User {login:"ikwattro"})-[:MEMBER_OF]->(o2:Organization {name:"neo4j"})');
    }

    public function testCreatingRepositoryLanguage()
    {
        $this->clearDb();
        $repo = new GithubRepository('neo4j-reco');
        $language = new Language("java");
        $repo->addLangage($language, 3000);
        $this->em->persist($repo);
        $this->em->flush();
        $this->assertGraphExist('(r:Repository {name:"neo4j-reco"})-[r2:WRITTEN_IN {linesOfCode: 3000}]->(l:Language {name:"java"})');
    }

    public function testFetchingRepositoryLanguages()
    {
        $this->clearDb();
        $this->client->run('CREATE (r:Repository {name:"neo4j-reco"})-[r2:WRITTEN_IN {linesOfCode: 3000}]->(l:Language {name:"java"}), 
        (u:User {login:"ikwattro"})-[:OWNS]->(r)');
        /** @var GithubRepository $repo */
        $repo = $this->em->getRepository(GithubRepository::class)->findOneBy('name', 'neo4j-reco');
        $this->assertEquals('ikwattro', $repo->getOwner()->getLogin());
        $this->assertCount(1, $repo->getWrittenLanguages());
    }

    /**
     * @param $login
     * @return \GraphAware\Neo4j\OGM\Tests\Integration\UseCase\Github\Model\GithubUser
     */
    private function createUser($login)
    {
        $user = new GithubUser($login);
        $user->setDescription("neo4j consultant");
        $this->em->persist($user);
        $this->em->flush();
        $this->assertGraphExist('(u:User {login:"'.$login.'", description:"neo4j consultant"})');

        return $user;
    }
}