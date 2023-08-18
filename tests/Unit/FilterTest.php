<?php

namespace App\Tests\Unit;

use App\Services\Filter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FilterTest extends KernelTestCase
{
    private ?Filter $filter;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->filter = $container->get(Filter::class);
    }

    public function testIsAllowedFilter(): void
    {
        $this->assertTrue($this->filter->isAllowedFilter('recent'));
        $this->assertTrue($this->filter->isAllowedFilter('likes'));
        $this->assertTrue($this->filter->isAllowedFilter('views'));
        $this->assertFalse($this->filter->isAllowedFilter('invalidFilter'));
    }
}
