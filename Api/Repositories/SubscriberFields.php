<?php

namespace Api\Repositories;

use Api\Libraries\MySQL;
use Api\Models\Field;
use Api\Models\SubscriberField;
use PDO;

class SubscriberFields implements SubscriberFieldsRepositoryInterface
{
    private $connection;
    public const MASS_ASSIGN = [
        'field_id',
        'subscriber_id',
        'value'
    ];

    public function __construct(MySQL $db)
    {
        $this->connection = $db->getConnection();
    }

    public function findForSubscriber(int $id): array
    {
        $sql = <<<sql
        SELECT sf.id, sf.field_id, sf.subscriber_id, sf.value, sf.created_at, 
        f.id as field_id, f.title, f.type, f.default_value 
        FROM `subscribers_fileds` sf 
        LEFT JOIN `fields` f ON f.id = sf.field_id
        WHERE sf.subscriber_id = :subscriber_id
        ORDER BY `id` DESC 
        sql;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":subscriber_id", $id, PDO::PARAM_INT);
        $statement->execute();

        $objects = [];
        while ($obj = $statement->fetch()) {
            $item = new SubscriberField($obj['value']);
            $item->setId($obj['id']);

            $field = new Field($obj['title'], $obj['type'], $obj['description'], $obj['default_value']);
            $field->setId($obj['field_id']);
            $item->setField($field);

            $objects[] = $item;
        }

        return $objects;
    }

    public function list(int $limit = 10, int $offset = 0): array
    {
        // TODO
        return [];
    }

    public function findById(float $id)
    {
        // TODO: Implement findById() method.
    }

    public function findFieldForSubscriber(int $subscriber_id, int $field_id)
    {
        $sql = <<<sql
        SELECT sf.id, sf.field_id, sf.subscriber_id, sf.value, sf.created_at, 
        f.id as field_id, f.title, f.type, f.default_value 
        FROM `subscribers_fileds` sf 
        LEFT JOIN `fields` f ON f.id = sf.field_id
        WHERE sf.subscriber_id = :subscriber_id AND sf.field_id = :field_id
        ORDER BY `id` DESC 
        sql;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":subscriber_id", $subscriber_id, PDO::PARAM_INT);
        $statement->bindValue(":field_id", $field_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function save(SubscriberField $field)
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
            INSERT INTO `subscribers_fileds` ({$columns}) VALUES ({$values});
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
            UPDATE `subscribers_fileds` SET {$columns_sql} WHERE `id`=:id;
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
