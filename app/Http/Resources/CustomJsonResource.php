<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class CustomJsonResource extends JsonResource
{
    private readonly string $key;

    public function __construct(
        $resource,
        private readonly string $message = 'Info.',
        private readonly int $code = 200,
        string $key = 'data'
    ) {
        parent::__construct($resource);
        $this->key = $key;
    }

    public function toArray(Request $request): array
    {
        //Paginating support
        if ($this->resource instanceof LengthAwarePaginator) {
            return [
                'status' => $this->code,
                'message' => $this->message,
                $this->key => $this->resource->items(),
                'meta' => [
                    'pagination' => [
                        'current_page' => $this->resource->currentPage(),
                        'per_page' => $this->resource->perPage(),
                        'total' => $this->resource->total(),
                        'last_page' => $this->resource->lastPage(),
                    ],
                ]
            ];
        }

        $data = $this->resource instanceof Model || $this->resource instanceof Collection
            ? $this->resource->toArray()
            : (array) $this->resource;

        return [
            'status' => $this->code,
            'message' => $this->message,
            $this->key => $data
        ];
    }

    /**
     * Parse data dengan opsi filter field tertentu, termasuk nested array
     * contoh isi dengan ['name','email','profile.address' jika nested array]
     * @param array|null $fields Field utama yang ingin ditampilkan (opsional)
     * @return array
     */
    public function filterFields(array $fields = null): array
    {
        $data = $this->resource;
        //Paginating support
        if ($this->resource instanceof LengthAwarePaginator) {
            return [
                'status' => $this->code,
                'message' => $this->message,
                $this->key => $this->filterCollection($this->resource->items(), $fields),
                'meta' => [
                    'pagination' => [
                        'current_page' => $this->resource->currentPage(),
                        'per_page' => $this->resource->perPage(),
                        'total' => $this->resource->total(),
                        'last_page' => $this->resource->lastPage(),
                    ],
                    //TODO: additional key
                ]

            ];
        }

        $data = $this->resource instanceof Model || $this->resource instanceof Collection
            ? $this->resource->toArray()
            : (array) $this->resource;

        // Jika resource adalah Collection (banyak model)
        if ($this->resource instanceof Collection) {
            $data = $this->filterCollection($data, $fields);
        } else {
            // Jika hanya satu model
            $data = $this->filterNestedFields($data, $fields);
        }
        return [
            'status' => $this->code,
            'message' => $this->message,
            $this->key => $data
        ];
    }

    /**
     * Filter semua item dalam Collection.
     *
     * @param array $items
     * @param array|null $fields
     * @return array
     */
    private function filterCollection(array $items, ?array $fields): array
    {
        return collect($items)->map(fn ($item) => $this->filterNestedFields($item instanceof Model ? $item->toArray() : $item, $fields))->toArray();
    }

    /**
     * Filter data termasuk nested array berdasarkan field yang diberikan
     *
     * @param array $data Data asli dari resource
     * @param array $fields Field yang diperbolehkan (bisa nested, contoh: ['name', 'email', 'profile.address'])
     * @return array
     */
    private function filterNestedFields(array $data, array $fields): array
    {
        $filteredData = [];

        foreach ($fields as $field) {
            $keys = explode('.', $field); // Cek jika ada nested field/array
            $this->assignNestedValue($filteredData, $keys, $data);
        }

        return $filteredData;
    }

    /**
     * Rekursif untuk memasukkan nilai ke dalam array yang telah difilter
     *
     * @param array &$filteredData Output data yang sudah difilter
     * @param array $keys Key yang akan diakses (bisa nested)
     * @param array $data Data sumber yang akan difilter
     */
    private function assignNestedValue(array &$filteredData, array $keys, array $data): void
    {
        $key = array_shift($keys);

        if (isset($data[$key])) {
            if (count($keys) > 0 && is_array($data[$key])) {
                if (!isset($filteredData[$key])) {
                    $filteredData[$key] = [];
                }
                $this->assignNestedValue($filteredData[$key], $keys, $data[$key]);
            } else {
                $filteredData[$key] = $data[$key];
            }
        }
    }
}
