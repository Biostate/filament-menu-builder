<?php

namespace Biostate\FilamentMenuBuilder\Tests\Enums;

use Biostate\FilamentMenuBuilder\Enums\MenuItemTarget;
use Biostate\FilamentMenuBuilder\Tests\TestCase;

class MenuItemTargetTest extends TestCase
{
    public function test_enum_values(): void
    {
        $this->assertEquals('_self', MenuItemTarget::Self->value);
        $this->assertEquals('_blank', MenuItemTarget::Blank->value);
    }

    public function test_get_label_method(): void
    {
        $selfLabel = MenuItemTarget::Self->getLabel();
        $blankLabel = MenuItemTarget::Blank->getLabel();
        
        $this->assertNotNull($selfLabel);
        $this->assertNotNull($blankLabel);
        $this->assertIsString($selfLabel);
        $this->assertIsString($blankLabel);
    }

    public function test_translation_loading(): void
    {
        // Test that labels are loaded from translation files
        $selfLabel = MenuItemTarget::Self->getLabel();
        $blankLabel = MenuItemTarget::Blank->getLabel();
        
        // Labels should be translation keys or actual translated text
        $this->assertNotEmpty($selfLabel);
        $this->assertNotEmpty($blankLabel);
    }

    public function test_all_cases_have_labels(): void
    {
        foreach (MenuItemTarget::cases() as $case) {
            $label = $case->getLabel();
            $this->assertNotNull($label);
            $this->assertIsString($label);
            $this->assertNotEmpty($label);
        }
    }

    public function test_enum_cases_count(): void
    {
        $cases = MenuItemTarget::cases();
        $this->assertCount(2, $cases);
    }

    public function test_enum_has_expected_cases(): void
    {
        $cases = MenuItemTarget::cases();
        $caseValues = array_map(fn($case) => $case->value, $cases);
        
        $this->assertContains('_self', $caseValues);
        $this->assertContains('_blank', $caseValues);
    }

    public function test_enum_values_are_valid_html_targets(): void
    {
        $cases = MenuItemTarget::cases();
        
        foreach ($cases as $case) {
            // HTML target values should start with underscore
            $this->assertStringStartsWith('_', $case->value);
        }
    }
}
