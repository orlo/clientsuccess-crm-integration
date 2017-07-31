<?php

namespace SocialSignIn\Test\ExampleCrmIntegration\Person;

use SocialSignIn\ExampleCrmIntegration\Person\Entity;

/**
 * @covers \SocialSignIn\ExampleCrmIntegration\Person\Entity
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{

    public function testEntity()
    {
        $person = new Entity('1', 'John');
        $this->assertSame('1', $person->getId());
        $this->assertSame('John', $person->getName());
        $this->assertJsonStringEqualsJsonString('{"id":"1","name":"John"}', json_encode($person));
    }
}
