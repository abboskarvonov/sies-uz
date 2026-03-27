<?php

namespace App\Services;

use Throwable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HemisApiService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.hemis_api.base_url'), '/');
        $this->token   = config('services.hemis_api.token', '');
    }

    /**
     * Barcha sahifalarni aylanib, items Collection qaytaradi.
     */
    public function fetchAll(string $endpoint, array $params = []): Collection
    {
        $items = collect();
        $page  = 1;

        do {
            $response = $this->get($endpoint, array_merge($params, ['page' => $page]));

            if (empty($response['data']['items'])) {
                break;
            }

            $items = $items->merge($response['data']['items']);

            $pagination = $response['data']['pagination'] ?? [];
            $pageCount  = $pagination['pageCount'] ?? 1;

            $page++;
        } while ($page <= $pageCount);

        return $items;
    }

    /**
     * Bitta xodimni hemis_employee_id bo'yicha topadi.
     */
    public function fetchEmployee(string $employeeId): ?array
    {
        $response = $this->get('data/employee-list', ['type' => 11, 'employee_id' => $employeeId]);
        $items    = $response['data']['items'] ?? [];

        foreach ($items as $item) {
            if ((string) ($item['id'] ?? '') === $employeeId) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Xodimning barcha lavozimlari (HEMIS da bir xodim bir nechta bo'limda bo'lishi mumkin).
     *
     * HEMIS API da bitta employee_id bo'yicha bir nechta yozuv kelishi mumkin —
     * har biri turli departmentdagi lavozimni ifodalaydi.
     *
     * @param  string  $employeeId  HEMIS employee ID (hemis_id yoki hemis_employee_id)
     */
    public function fetchEmployeePositions(string $employeeId): Collection
    {
        return $this->fetchAll('data/employee-list', [
            'type'        => 11,
            'employee_id' => $employeeId,
        ]);
    }

    /**
     * UUID bo'yicha xodimning barcha lavozimlarini oladi.
     * UUID OAuth tokendan olinadi va employee_id dan farq qilishi mumkin.
     */
    public function fetchEmployeePositionsByUuid(string $uuid): Collection
    {
        return $this->fetchAll('data/employee-list', [
            'type' => 11,
            'uuid' => $uuid,
        ]);
    }

    /**
     * Bitta so'rov yuboradi, array qaytaradi.
     */
    public function get(string $endpoint, array $params = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        try {
            $response = Http::withToken($this->token)
                ->timeout(30)
                ->get($url, $params);

            if ($response->failed()) {
                Log::error('HEMIS API error', [
                    'url'    => $url,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return [];
            }

            return $response->json() ?? [];
        } catch (Throwable $e) {
            Log::error('HEMIS API exception', [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
