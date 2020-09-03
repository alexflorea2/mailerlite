<?php

namespace Api\Repositories;

use Api\Libraries\MySQL;
use Api\Models\Field;
use PDO;

class Fields implements FieldsRepositoryInterface
{
    private $connection;
    public const MASS_ASSIGN = [
        'title',
        'description',
        'type',
        'default_value',
    ];

    public function __construct(MySQL $db)
    {
        $this->connection = $db->getConnection();
    }

    public function list(int $limit = 500, int $offset = 0): array
    {
        $sql = <<<sql
        SELECT * FROM `fields` ORDER BY `id` DESC LIMIT :offset, :limit;
        sql;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":offset", $offset, PDO::PARAM_INT);
        $statement->bindValue(":limit", $limit, PDO::PARAM_INT);
        $statement->execute();

        $objects = [];
        while ($obj = $statement->fetch()) {
            $field = new Field($obj['title'], $obj['type'], $obj['description'], $obj['default_value']);
            $field->setId($obj['id']);
            $objects[] = $field;
        }

        return $objects;
    }

    public function findById(float $id)
    {
        // TODO: Implement findById() method.
    }

    public function findByTitle(string $title)
    {
        $sql = <<<sql
        SELECT * FROM `fields` WHERE `title`= :title;
        sql;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":title", $title, PDO::PARAM_STR);
        $statement->execute();

        $obj = $statement->fetch();

        if ($obj !== false) {
            $field = new Field($obj['title'], $obj['type'], $obj['description'], $obj['default_value']);
            $field->setId($obj['id']);

            return $field;
        }

        return false;
    }

    public function save(Field $field)
    {
        $id = $field->getId();
        $fieldArray = $field->toArray();

        if (!isset($id)) {
            $columns = implode(
                ',',
                array_map(
                    function ($e) {
                        return "`{$e}`";
                    },
                    self::MASS_ASSIGN
                )
            );

            $values = implode(
                ',',
                array_map(
                    function ($e) {
                        return ":{$e}";
                    },
                    self::MASS_ASSIGN
                )
            );

            $sql = <<<sql
            INSERT INTO `fields` ({$columns}) VALUES ({$values});
            sql;

            $statement = $this->connection->prepare($sql);

            foreach (self::MASS_ASSIGN as $i) {
                $statement->bindValue(":{$i}", $fieldArray[$i], PDO::PARAM_STR);
            }
            $statement->execute();

            return $this->connection->lastInsertId();
        } else {
            $columns = [];
            foreach (self::MASS_ASSIGN as $column_name) {
                $columns[] = "{$column_name}=:{$column_name}";
            }
            $columns_sql = implode(',', $columns);

            $sql = <<<sql
            UPDATE `fields` SET {$columns_sql} WHERE `id`=:id;
            sql;

            $statement = $this->connection->prepare($sql);

            foreach (self::MASS_ASSIGN as $i) {
                $statement->bindValue(":{$i}", $fieldArray[$i], PDO::PARAM_STR);
            }
            $statement->bindValue(":id", $id, PDO::PARAM_INT);

            $statement->execute();

            return $id;
        } // END Update
    }
}
