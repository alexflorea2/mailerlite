<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class SubscriberTest extends TestCase
{
    public function testCannotAddWithoutValidEmail() : void
    {
        $this->expectException(\Exception::class);
        $subscriber = new \Api\Models\Subscriber('not_an_email','John Doe');
    }

    public function testAddingFields(): void
    {
        $subscriber = new \Api\Models\Subscriber('test@example.org','John Doe');
        $random = rand(1,10);
        $i = 1;
        while ($i<=$random)
        {
            $subscriber->addField( new \Api\Models\SubscriberField("value {$i}") );
            $i++;
        }

        $fields = $subscriber->getFields();

        $this->assertEquals(
            $random,
            count($fields)
        );
    }

    public function testToArray() : void
    {
        $subscriber = new \Api\Models\Subscriber('test@example.org','John Doe');
        $toArray = $subscriber->toArray();

        $this->assertEquals(
            $toArray,
            [
                'id' => null,
                'email' => 'test@example.org',
                'name' => 'John Doe',
                'state' => \Api\Models\Subscriber::STATE_UNCONFIRMED,
                'source' => null
            ]
        );
    }
}