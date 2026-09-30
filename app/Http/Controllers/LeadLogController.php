<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Receives fire-and-forget lead breadcrumbs from the SPA.
 * Written to storage/logs/leads-YYYY-MM-DD.jsonl for recovery when Telegram fails.
 */
class LeadLogController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();

        // SPA may send text/plain (sendBeacon / CORS-simple) with raw JSON body
        if ($data === [] || !$request->has('form')) {
            $raw = $request->getContent();
            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $data = $decoded;
                    $request->merge($decoded);
                }
            }
        }

        $validator = Validator::make($data, [
            'id' => 'nullable|string|max:64',
            'ts' => 'nullable|string|max:40',
            'form' => 'required|string|max:80',
            'stage' => 'required|string|max:40',
            'payload' => 'nullable|array',
            'error' => 'nullable|string|max:2000',
            'httpStatus' => 'nullable|integer',
            'href' => 'nullable|string|max:1000',
            'referrer' => 'nullable|string|max:1000',
            'userAgent' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false], 422);
        }

        $row = [
            'id' => $data['id'] ?? null,
            'ts' => $data['ts'] ?? now()->toIso8601String(),
            'form' => $data['form'],
            'stage' => $data['stage'],
            'payload' => $data['payload'] ?? [],
            'error' => $data['error'] ?? null,
            'httpStatus' => $data['httpStatus'] ?? null,
            'href' => $data['href'] ?? null,
            'referrer' => $data['referrer'] ?? null,
            'userAgent' => $data['userAgent'] ?? null,
            'ip' => $request->ip(),
            'received_at' => now()->toIso8601String(),
        ];

        try {
            $dir = storage_path('logs');
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $file = $dir . '/leads-' . now()->format('Y-m-d') . '.jsonl';
            File::append($file, json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL);

            // Also mirror errors into laravel.log for quick tailing
            if (in_array($row['stage'], ['error', 'validation'], true)) {
                Log::warning('SPA lead event', $row);
            } else {
                Log::info('SPA lead event', [
                    'form' => $row['form'],
                    'stage' => $row['stage'],
                    'phone' => $row['payload']['phone'] ?? null,
                    'name' => $row['payload']['name'] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Lead log write failed', ['error' => $e->getMessage(), 'row' => $row]);
            return response()->json(['success' => false], 500);
        }

        return response()->json(['success' => true]);
    }
}
