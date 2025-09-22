<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class GoogleReviewsController extends Controller
{
    public function show(): View
    {
        $placeId = config('services.google.place_id');
        $apiKey  = config('services.google.maps_api_key');
        $lang    = config('services.google.lang');
        $ttl     = now()->addHours(config('services.google.cache_hours', 12));

        $payload = Cache::remember("google_place_details_{$placeId}_{$lang}", $ttl, function () use ($placeId, $apiKey, $lang) {
            $resp = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => $placeId,
                'fields'   => 'name,rating,user_ratings_total,url,reviews',
                'language' => $lang,
                'key'      => $apiKey,
            ]);

            abort_unless($resp->ok(), 502, 'Google API error');

            $data = $resp->json('result', []);
            $reviews = collect($data['reviews'] ?? [])
                ->filter(fn ($r) => !empty($r['text'])) // only reviews with text
                ->take(3)                               // show up to 3 (API max 5)
                ->map(function ($r) {
                    return [
                        'author_name' => $r['author_name'] ?? 'Anonymous',
                        'profile_photo_url' => $r['profile_photo_url'] ?? null,
                        'rating' => $r['rating'] ?? null,
                        'time_desc' => $r['relative_time_description'] ?? null,
                        'text' => $r['text'] ?? '',
                    ];
                })
                ->values()
                ->all();

            return [
                'name'   => $data['name'] ?? null,
                'rating' => $data['rating'] ?? null,
                'total'  => $data['user_ratings_total'] ?? null,
                'url'    => $data['url'] ?? null,
                'reviews'=> $reviews,
            ];
        });

        return view('widgets.google-reviews', $payload);
    }
}
