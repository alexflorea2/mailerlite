<?php

namespace Api\Repositories;

use Api\Libraries\MySQL;
use Api\Models\Subscriber;
use PDO;

class Subscribers implements SubscribersRepositoryInterface
{
    private $connection;
    public const MASS_ASSIGN = [
        'email',
        'name',
        'state',
        'source',
    ];

    public function __construct(MySQL $db)
    {
        $this->connection = $db->getConnection();
    }

    public function meta(int $lastID, int $take, ?string $query = null, ?string $state = null)
    {
        $meta = [
            'more' => false,
            'total' => 0,
        ];

        $sql_params = [];

        $sql = <<<sql
        SELECT COUNT(*) as total FROM `subscribers` WHERE 1=1 
        sql;

        if (!is_null($state)) {
            $sql .= <<<sql
 AND `state`=:state 
sql;
            $sql_params['state'] = $state;
        }

        if (!is_null($query)) {
            $sql .= <<<sql
 AND (`email` LIKE :like_email OR `name` LIKE :like_name )
sql;
            $sql_params['like_email'] = "%{$query}%";
            $sql_params['like_name'] = "%{$query}%";
        }

        $statement = $this->connection->prepare($sql);
        if (count($sql_params) > 0) {
            foreach ($sql_params as $column_name => $column_value) {
                $statement->bindValue(":{$column_name}", $column_value, PDO::PARAM_STR);
            }
        }
        $statement->execute();

        $result = $statement->fetch();

        if ($result !== false) {
            $meta['total'] = $result['total'];

            $sql_params = [];

            $sql = <<<sql
        SELECT COUNT(*) as total FROM `subscribers` WHERE `id`>:id 
        sql;

            if (!is_null($state)) {
                $sql .= <<<sql
 AND `state`=:state 
sql;
                $sql_params['state'] = $state;
            }

            if (!is_null($query)) {
                $sql .= <<<sql
 AND (`email` LIKE :like_email OR `name` LIKE :like_name )
sql;
                $sql_params['like_email'] = "%{$query}%";
                $sql_params['like_name'] = "%{$query}%";
            }

            $sql .= " LIMIT 1";

            $statement = $this->connection->prepare($sql);
            $statement->bindValue(":id", $lastID, PDO::PARAM_INT);
            if (count($sql_params) > 0) {
                foreach ($sql_params as $column_name => $column_value) {
                    $statement->bindValue(":{$column_name}", $column_value, PDO::PARAM_STR);
                }
            }
            $statement->execute();

            $result = $statement->fetch();

            if ($lastID == 0) {
                if ($result['total'] > $take) {
                    $meta['more'] = true;
                }
            } else {
                if ($result['total'] > 1) {
                    $meta['more'] = true;
                }
            }
        }

        return $meta;
    }

    public function list(int $lastID = 0, int $take = 10, ?string $query = null, ?string $state = null): array
    {
        $sql_params = [];

        $sql = <<<sql
        SELECT * FROM `subscribers`  WHERE `id`>:cursor
        sql;

        if (!is_null($state)) {
            $sql .= <<<sql
 AND `state`=:state 
sql;
            $sql_params['state'] = $state;
        }

        if (!is_null($query)) {
            $sql .= <<<sql
 AND (`email` LIKE :like_email OR `name` LIKE :like_name )
sql;
            $sql_params['like_email'] = "%{$query}%";
            $sql_params['like_name'] = "%{$query}%";
        }

        $sql .= <<<sql
  ORDER BY `id` ASC LIMIT :limit
sql;


        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":cursor", $lastID, PDO::PARAM_INT);
        $statement->bindValue(":limit", $take, PDO::PARAM_INT);
        if (count($sql_params) > 0) {
            foreach ($sql_params as $column_name => $column_value) {
                $statement->bindValue(":{$column_name}", $column_value, PDO::PARAM_STR);
            }
        }
        $statement->execute();

        $objects = [];
        while ($obj = $statement->fetch()) {
            $subscriber = new Subscriber(
                $obj['email'],
                $obj['name'],
                $obj['state'],
                $obj['source'],
            );
            $subscriber->setId($obj['id']);
            $objects[] = $subscriber;
        }

        return $objects;
    }

    public function findById(float $id): Subscriber
    {
        $sql = <<<sql
        SELECT * FROM `subscribers` WHERE `id`= :id;
        sql;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();

        $obj = $statement->fetch();

        if ($obj !== false) {
            $subscriber = new Subscriber(
                $obj['email'],
                $obj['name'],
                $obj['state'],
                $obj['source']
            );
            $subscriber->setId($obj['id']);

            return $subscriber;
        }

        throw new ResourceNotFoundException();
    }

    public function findByEmail(string $email)
    {
        $sql = <<<sql
        SELECT * FROM `subscribers` WHERE `email`= :email;
        sql;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":email", $email, PDO::PARAM_STR);
        $statement->execute();

        $obj = $statement->fetch();

        if ($obj !== false) {
            $subscriber = new Subscriber(
                $obj['email'],
                $obj['name'],
                $obj['state'],
                $obj['source']
            );
            $subscriber->setId($obj['id']);

            return $subscriber;
        }

        throw new ResourceNotFoundException();
    }

    public function delete(int $id)
    {
        $sql = <<<sql
        DELETE FROM `subscribers` WHERE `id`=:id;
        sql;

        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function save(Subscriber $subscriber)
    {
        $id = $subscriber->getId();
        $subscriberArray = $subscriber->toArray();

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
            INSERT INTO `subscribers` ({$columns}) VALUES ({$values});
            sql;

            $statement = $this->connection->prepare($sql);

            foreach (self::MASS_ASSIGN as $i) {
                $statement->bindValue(":{$i}", $subscriberArray[$i], PDO::PARAM_STR);
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
            UPDATE `subscribers` SET {$columns_sql} WHERE `id`=:id;
            sql;

            $statement = $this->connection->prepare($sql);

            foreach (self::MASS_ASSIGN as $i) {
                $statement->bindValue(":{$i}", $subscriberArray[$i], PDO::PARAM_STR);
            }
            $statement->bindValue(":id", $id, PDO::PARAM_INT);

            $statement->execute();

            return $id;
        } // END Update
    }
}
