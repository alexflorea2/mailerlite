<?php

if (php_sapi_name() !== "cli") {
    echo "Can only be run from command line".
    exit;
}

$commands = [
    'seed'
];

if( !isset( $argv[1] )  )
{
    echo "Available commands:" .PHP_EOL;
    echo "1. seed {no of fake subscribers}: will seed database with fake data. Default 1000 items.".PHP_EOL;
    exit;
}
if( !in_array($argv[1], $commands) )
{
    echo "Available commands:" .PHP_EOL;
    echo "1. seed {no of fake subscribers}: will seed database with fake data".PHP_EOL;
    exit;
}

$command = $argv[1];

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../defines.php';
require __DIR__ . '/../global_helpers.php';

use Api\Models\Field;
use Api\Models\Subscriber;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\{Request, JsonResponse};
use DI\ContainerBuilder;
use Api\Libraries\MySQL;
use Api\Repositories\{Fields, FieldsRepositoryInterface, Subscribers, SubscribersRepositoryInterface};

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env');

if (isset($_ENV['DB_HOST']) && $_ENV['DB_HOST'] === ENV_DEVELOPMENT) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

$mysql = new MySQL(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD'],
    $_ENV['DB_NAME']
);

switch ( $command )
{
    case 'seed':
        // Fields
        $faker = Faker\Factory::create();
        echo "Creating 10 random fields" . PHP_EOL;

        $faker->addProvider(new Faker\Provider\Internet($faker));
        $faker->addProvider(new Faker\Provider\en_US\Company($faker));

        $fieldsRepository = new Fields($mysql);

        for ($i = 0; $i < 10; $i++) {
            $field = new Field(
                $faker->domainWord,
                $faker->randomElement(Field::ALLOWED_TYPES),
                $faker->bs
            );

            $fieldsRepository->save($field);
        }

        // Subscribers

        $limit = $argv[2] ?? 1000;

        echo "Creating {$limit} random subscribers" . PHP_EOL;

        $subscribersRepository = new Subscribers($mysql);

        for ($i = 0; $i < $limit; $i++) {
            $subscriber = new Subscriber(
                $faker->email,
                $faker->name,
                $faker->randomElement(Subscriber::ALLOWED_STATES),
                $faker->randomElement(['Api','InternalAll','WebForm','ExternalServiceX','ExternalServiceY'])
            );

            $subscribersRepository->save($subscriber);
        }

        echo "Operation ended".PHP_EOL;

        break;
}

exit;