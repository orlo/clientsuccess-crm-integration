<?php

namespace SocialSignIn\Test\ExampleCrmIntegration\Person;

use SocialSignIn\ExampleCrmIntegration\Person\Entity;
use SocialSignIn\ExampleCrmIntegration\Person\MockRepository;

/**
 * @covers \SocialSignIn\ExampleCrmIntegration\Person\MockRepository
 */
class MockRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var MockRepository
     */
    private $repository;

    public function setUp()
    {
        $this->repository = new MockRepository();
    }

    public function testSearch()
    {
        $persons = $this->repository->search('jo');
        $this->assertCount(2, $persons);
        $this->assertInstanceOf(Entity::class, $persons[0]);
        $this->assertInstanceOf(Entity::class, $persons[1]);
    }

    public function testGet()
    {
        $person = $this->repository->get('1');
        $this->assertInstanceOf(Entity::class, $person);

        $person = $this->repository->get('1000');
        $this->assertSame(null, $person);
    }
}
