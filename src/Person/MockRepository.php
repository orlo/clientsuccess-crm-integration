<?php

namespace SocialSignIn\ExampleCrmIntegration\Person;

final class MockRepository implements RepositoryInterface
{

    private $persons = [];

    public function __construct()
    {
        $this->persons['1'] = new Entity('1', 'John Smith');
        $this->persons['2'] = new Entity('2', 'Michael Khan');
        $this->persons['3'] = new Entity('3', 'Sebastian Roberts');
        $this->persons['4'] = new Entity('4', 'Laura Jones');
    }

    /**
     * @param string $query
     *
     * @return Entity[]
     */
    public function search($query)
    {
        $query = preg_quote($query, '!');

        $matched = [];

        foreach ($this->persons as $person) {
            if (preg_match('!' . $query . '!i', $person->getName())) {
                $matched[] = $person;
            }
        }

        return $matched;
    }

    /**
     * @param string $id
     *
     * @return Entity|null
     */
    public function get($id)
    {
        if (isset($this->persons[$id])) {
            return $this->persons[$id];
        }
        return null;
    }
}
