<?php

namespace Tests\Unit;

use App\Traits\HasTableFilters;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class HasTableFiltersTest extends TestCase
{
    private object $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new class
        {
            use HasTableFilters;

            /**
             * @param  array<string>  $allowedSorts
             * @return array{search: ?string, rows: int, order: string, direction: 'asc'|'desc'}
             */
            public function filters(
                Request $request,
                array $allowedSorts = [],
                string $defaultSort = 'created_at',
                string $defaultDirection = 'desc'
            ): array {
                return $this->getTableFilters($request, $allowedSorts, $defaultSort, $defaultDirection);
            }
        };
    }

    public function test_returns_default_values_when_request_is_empty(): void
    {
        $request = new Request;
        $filters = $this->service->filters($request);

        $this->assertNull($filters['search']);
        $this->assertSame(10, $filters['rows']);
        $this->assertSame('created_at', $filters['order']);
        $this->assertSame('desc', $filters['direction']);
    }

    public function test_sanitizes_search_by_trimming_and_converting_empty_to_null(): void
    {
        $requestWithSpaces = new Request(['search' => '  john doe  ']);
        $filters1 = $this->service->filters($requestWithSpaces);
        $this->assertSame('john doe', $filters1['search']);

        $requestEmpty = new Request(['search' => '   ']);
        $filters2 = $this->service->filters($requestEmpty);
        $this->assertNull($filters2['search']);
    }

    public function test_normalizes_rows_to_allowed_values(): void
    {
        foreach ([5, 10, 25, 50, 100] as $allowedRow) {
            $request = new Request(['rows' => $allowedRow]);
            $filters = $this->service->filters($request);
            $this->assertSame($allowedRow, $filters['rows']);
        }

        $requestInvalid = new Request(['rows' => 99999]);
        $filtersInvalid = $this->service->filters($requestInvalid);
        $this->assertSame(10, $filtersInvalid['rows']);

        $requestNegative = new Request(['rows' => -5]);
        $filtersNegative = $this->service->filters($requestNegative);
        $this->assertSame(10, $filtersNegative['rows']);

        $requestString = new Request(['rows' => 'invalid']);
        $filtersString = $this->service->filters($requestString);
        $this->assertSame(10, $filtersString['rows']);
    }

    public function test_validates_order_against_allowed_sorts_whitelist(): void
    {
        $allowedSorts = ['name', 'email', 'created_at'];

        $requestValid = new Request(['order' => 'email']);
        $filtersValid = $this->service->filters($requestValid, $allowedSorts);
        $this->assertSame('email', $filtersValid['order']);

        $requestMalicious = new Request(['order' => 'password; DROP TABLE users;--']);
        $filtersMalicious = $this->service->filters($requestMalicious, $allowedSorts);
        $this->assertSame('created_at', $filtersMalicious['order']);

        $requestInvalid = new Request(['order' => 'non_existent_column']);
        $filtersInvalid = $this->service->filters($requestInvalid, $allowedSorts);
        $this->assertSame('created_at', $filtersInvalid['order']);
    }

    public function test_normalizes_direction_to_asc_or_desc(): void
    {
        $requestAsc = new Request(['direction' => 'asc']);
        $filtersAsc = $this->service->filters($requestAsc);
        $this->assertSame('asc', $filtersAsc['direction']);

        $requestDesc = new Request(['direction' => 'desc']);
        $filtersDesc = $this->service->filters($requestDesc);
        $this->assertSame('desc', $filtersDesc['direction']);

        $requestUppercase = new Request(['direction' => 'ASC']);
        $filtersUppercase = $this->service->filters($requestUppercase);
        $this->assertSame('asc', $filtersUppercase['direction']);

        $requestInvalid = new Request(['direction' => 'random_dir']);
        $filtersInvalid = $this->service->filters($requestInvalid);
        $this->assertSame('desc', $filtersInvalid['direction']);
    }

    public function test_respects_custom_default_sort_and_direction(): void
    {
        $request = new Request;
        $filters = $this->service->filters(
            request: $request,
            allowedSorts: ['title', 'published_at'],
            defaultSort: 'published_at',
            defaultDirection: 'asc'
        );

        $this->assertSame('published_at', $filters['order']);
        $this->assertSame('asc', $filters['direction']);
    }
}
