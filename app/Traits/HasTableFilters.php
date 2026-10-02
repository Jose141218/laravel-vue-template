<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasTableFilters
{
    /**
     * Extrae, sanitiza y valida los filtros base estándar de una tabla.
     *
     * @param  array<string>  $allowedSorts  Lista blanca de columnas permitidas para ordenar.
     * @return array{search: ?string, rows: int, order: string, direction: 'asc'|'desc'}
     */
    protected function getTableFilters(
        Request $request,
        array $allowedSorts = [],
        string $defaultSort = 'created_at',
        string $defaultDirection = 'desc'
    ): array {
        $search = $request->filled('search')
            ? trim((string) $request->input('search'))
            : null;

        $rows = (int) $request->input('rows', 10);
        $allowedRows = [5, 10, 25, 50, 100];
        if (! in_array($rows, $allowedRows, true)) {
            $rows = 10;
        }

        $order = (string) $request->input('order', $defaultSort);
        if (! empty($allowedSorts) && ! in_array($order, $allowedSorts, true)) {
            $order = $defaultSort;
        }

        $defaultDir = strtolower($defaultDirection) === 'asc' ? 'asc' : 'desc';
        $direction = match (strtolower((string) $request->input('direction', $defaultDir))) {
            'asc' => 'asc',
            'desc' => 'desc',
            default => $defaultDir,
        };

        return [
            'search' => $search,
            'rows' => $rows,
            'order' => $order,
            'direction' => $direction,
        ];
    }
}
