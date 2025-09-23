<?php

namespace Biostate\FilamentMenuBuilder\Tests\Enums;

use Biostate\FilamentMenuBuilder\Enums\MenuItemType;
use Biostate\FilamentMenuBuilder\Tests\TestCase;

class MenuItemTypeTest extends TestCase
{
    public function test_enum_values(): void
    {
        $this->assertEquals('link', MenuItemType::Link->value);
        $this->assertEquals('route', MenuItemType::Route->value);
        $this->assertEquals('model', MenuItemType::Model->value);
    }

    public function test_get_label_method(): void
    {
        $this->assertEquals('Link', MenuItemType::Link->getLabel());
        $this->assertEquals('Route', MenuItemType::Route->getLabel());
        $this->assertEquals('Model', MenuItemType::Model->getLabel());
    }

    public function test_from_value_method_with_valid_values(): void
    {
        $this->assertEquals(MenuItemType::Route, MenuItemType::fromValue('route'));
        $this->assertEquals(MenuItemType::Model, MenuItemType::fromValue('model'));
        $this->assertEquals(MenuItemType::Link, MenuItemType::fromValue('link'));
    }

    public function test_from_value_method_with_invalid_value(): void
    {
        $this->assertEquals(MenuItemType::Link, MenuItemType::fromValue('invalid'));
        $this->assertEquals(MenuItemType::Link, MenuItemType::fromValue(''));
        $this->assertEquals(MenuItemType::Link, MenuItemType::fromValue('unknown'));
    }

    public function test_all_cases_have_labels(): void
    {
        foreach (MenuItemType::cases() as $case) {
            $this->assertNotNull($case->getLabel());
            $this->assertIsString($case->getLabel());
            $this->assertNotEmpty($case->getLabel());
        }
    }

    public function test_enum_cases_count(): void
    {
        $cases = MenuItemType::cases();
        $this->assertCount(3, $cases);
    }

    public function test_enum_has_expected_cases(): void
    {
        $cases = MenuItemType::cases();
        $caseValues = array_map(fn ($case) => $case->value, $cases);

        $this->assertContains('link', $caseValues);
        $this->assertContains('route', $caseValues);
        $this->assertContains('model', $caseValues);
    }
}
