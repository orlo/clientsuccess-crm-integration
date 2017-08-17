<?php

namespace SocialSignIn\Test\ClientSuccessIntegration\Person;

use SocialSignIn\ClientSuccessIntegration\Person\Entity;

/**
 * @covers \SocialSignIn\ClientSuccessIntegration\Person\Entity
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
