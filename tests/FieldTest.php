<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class FieldTest extends TestCase
{
    public function testCannotAddUndefinedTypes() : void
    {
        $this->expectException(\Exception::class);
        $field = new \Api\Models\Field('test','unexpected_type');
    }

    public function testCannotResetId() : void
    {
        $field = new \Api\Models\Field('test','number');
        $field->setId(1);
        $this->expectException(\Exception::class);
        $field->setId(2);
    }
}