<?php

namespace Tests\Unit;

use App\Traits\HasDateFormats;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class HasDateFormatsTest extends TestCase
{
    private object $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new class
        {
            use HasDateFormats;

            public function format(Carbon|string|null $date, bool $withTime = true): ?array
            {
                return $this->formatDate($date, $withTime);
            }
        };
    }

    public function test_format_date_returns_null_when_null_given(): void
    {
        $this->assertNull($this->service->format(null));
    }

    public function test_format_date_with_time_by_default(): void
    {
        $date = Carbon::create(2026, 9, 22, 20, 30, 0);
        $result = $this->service->format($date);

        $this->assertNotNull($result);
        $this->assertSame('2026-09-22 20:30:00', $result['raw']);
        $this->assertSame('22/09/2026 20:30', $result['formatted']);
        $this->assertStringContainsString('22 de septiembre de 2026', $result['human']);
        $this->assertArrayHasKey('diff', $result);
    }

    public function test_format_date_without_time(): void
    {
        $date = Carbon::create(2026, 9, 22, 20, 30, 0);
        $result = $this->service->format($date, false);

        $this->assertNotNull($result);
        $this->assertSame('2026-09-22', $result['raw']);
        $this->assertSame('22/09/2026', $result['formatted']);
        $this->assertSame('22 de septiembre de 2026', $result['human']);
        $this->assertArrayHasKey('diff', $result);
    }

    public function test_format_date_from_string(): void
    {
        $result = $this->service->format('2026-09-22 15:45:00');

        $this->assertNotNull($result);
        $this->assertSame('2026-09-22 15:45:00', $result['raw']);
        $this->assertSame('22/09/2026 15:45', $result['formatted']);
    }
}
