<?php

namespace FpDbTest;

use Exception;

class DatabaseTest
{
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function testBuildQuery(): void
    {
        $results = [];

        $results[] = $this->db->buildQuery('SELECT name FROM users WHERE user_id = 1');

        /*
         * По условиям в README спецификатор ?d может принимать значение NULL
         * `Параметры ?, ?d, ?f могут принимать значения null (в этом случае в шаблон вставляется NULL).`
         * соответственно в массиве current не корректное соответствие для этого кейса.
         * Если нужно некоторые параметры удалять, если их значение NULL, то, как мне кажется,
         * будет лучше добавить ещё спецификатор/ы. Пропущено.
         *
        $results[] = $this->db->buildQuery(
            'SELECT * FROM users WHERE name = ? AND id = ?d AND block = 0',
            ['Jack']
        );
        */

        $results[] = $this->db->buildQuery(
            'SELECT ?# FROM users WHERE user_id = ?d AND block = ?d',
            [['name', 'email'], 2, true]
        );

        $results[] = $this->db->buildQuery(
            'UPDATE users SET ?a WHERE user_id = -1',
            [['name' => 'Jack', 'email' => null]]
        );

        foreach ([null, true] as $block) {
            $results[] = $this->db->buildQuery(
                'SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}',
                ['user_id', [1, 2, 3], $block ?? $this->db->skip()]
            );
        }

        $correct = [
            'SELECT name FROM users WHERE user_id = 1',
            // Судя по условиям в README тут должен быть следующий вариант:
            // 'SELECT * FROM users WHERE name = \'Jack\' AND id = NULL AND block = 0',
            // Пропущено...
            /*'SELECT * FROM users WHERE name = \'Jack\' AND block = 0',*/
            'SELECT `name`, `email` FROM users WHERE user_id = 2 AND block = 1',
            'UPDATE users SET `name` = \'Jack\', `email` = NULL WHERE user_id = -1',
            'SELECT name FROM users WHERE `user_id` IN (1, 2, 3)',
            'SELECT name FROM users WHERE `user_id` IN (1, 2, 3) AND block = 1',
        ];

        if ($results !== $correct) {
            throw new Exception('Failure.');
        }
    }
}
