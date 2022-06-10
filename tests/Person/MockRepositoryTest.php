<?php

namespace SocialSignIn\Test\ClientSuccessIntegration\Person;

use PHPUnit\Framework\TestCase;
use SocialSignIn\ClientSuccessIntegration\Person\Entity;
use SocialSignIn\ClientSuccessIntegration\Person\MockRepository;

/**
 * @covers \SocialSignIn\ClientSuccessIntegration\Person\MockRepository
 */
class MockRepositoryTest extends TestCase
{

    /**
     * @var MockRepository
     */
    private $repository;

    public function setUp(): void
    {
        $this->repository = new MockRepository();
    }

    public function testSearch(): void
    {
        $persons = $this->repository->search('jo');
        $this->assertCount(2, $persons);
        $this->assertInstanceOf(Entity::class, $persons[0]);
        $this->assertInstanceOf(Entity::class, $persons[1]);
    }

    public function testGet(): void
    {
        $person = $this->repository->get('1');
        $this->assertInstanceOf(Entity::class, $person);

        $person = $this->repository->get('1000');
        $this->assertSame(null, $person);
    }
}
