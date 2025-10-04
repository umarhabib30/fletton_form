<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class UserSurveyController extends Controller
{
    public function signup()
    {
        return view('user.survey.quote-form');
    }

    public function submitSurvey(Request $request)
    {
        // dd($request);
        $settings = Price::first();

        $marketValue = (float) $request->market_value;

        // --- Costs ---
        $listedCost = 0;
        if ($request->listed_building == 'yes') {
            $listedCost = $settings->listing_cost;
        }

        $sqftCost = 0;
        if ($request->over1650 === 'yes' && $request->sqft_area > 1650) {
            $sqftCost = ($request->sqft_area - 1650) * $settings->extra_sqft_cost;
        }

        $bedroomCost = 0;
        if ($request->number_of_bedrooms > 4) {
            $bedroomCost = ($request->number_of_bedrooms - 4) * $settings->extra_room_cost;
        }

        $additionalCost = $listedCost + $sqftCost + $bedroomCost;

        $level1 = $settings->level1_base + ($marketValue * $settings->level1_market_percentage) + $bedroomCost + $listedCost;
        $level2 = $settings->level2_base + ($marketValue * $settings->level2_market_percentage) + $additionalCost;
        $level3 = $settings->level3_base + ($marketValue * $settings->level3_market_percentage) + $additionalCost;
        $level4 = $settings->level4_base + ($marketValue * $settings->level4_market_percentage) + $additionalCost;

        // ✅ Generate payment URLs
        $level1_payment_url = "https://flettons.group/flettons-order/?email={$request->email_address}&total={$level1}&level=1&order=1";
        $level2_payment_url = "https://flettons.group/flettons-order/?email={$request->email_address}&total={$level2}&level=2&order=1";
        $level3_payment_url = "https://flettons.group/flettons-order/?email={$request->email_address}&total={$level3}&level=3&order=1";
        $level4_payment_url = "https://flettons.group/flettons-order/?email={$request->email_address}&total={$level4}&level=4&order=1";

        $payload = [
            'given_name' => $request->first_name,
            'family_name' => $request->last_name,
            'duplicate_option' => 'Email',
            // Billing address
            'addresses' => [
                [
                    'line1' => $request->full_address,
                    'locality' => '',
                    'postal_code' => $request->postcode ?? '',
                    'country_code' => '',
                    'field' => 'BILLING'
                ]
            ],
            // Phone numbers
            'phone_numbers' => [
                [
                    'number' => str_replace(' ', '', $request->telephone_number),
                    'field' => 'PHONE1'
                ]
            ],
            // Email addresses
            'email_addresses' => [
                [
                    'email' => $request->email_address,
                    'field' => 'EMAIL1'
                ]
            ],
            // property details
            'custom_fields' => [
                ['id' => '191', 'content' => $request->full_address],
                ['id' => '193', 'content' => (int) $request->market_value],
                ['id' => '195', 'content' => $request->house_or_flat],
                ['id' => '197', 'content' => (int) $request->number_of_bedrooms],
                ['id' => '203', 'content' => $request->listed_building],
                ['id' => '603', 'content' => (int) $request->sqft_area],
                // Totals
                ['id' => '220', 'content' => number_format($level1, 2)],
                ['id' => '224', 'content' => number_format($level2, 2)],
                ['id' => '228', 'content' => number_format($level3, 2)],
                ['id' => '238', 'content' => number_format($level4, 2)],
                // Payment links
                ['id' => '218', 'content' => $level1_payment_url],
                ['id' => '222', 'content' => $level2_payment_url],
                ['id' => '226', 'content' => $level3_payment_url],
                ['id' => '240', 'content' => $level4_payment_url],
            ],
        ];

        // ✅ Send to Keap
        $response = Http::withHeaders([
            'X-Keap-API-Key' => 'KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
            'Authorization' => 'Bearer KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
            'Content-Type' => 'application/json',
        ])->put('https://api.infusionsoft.com/crm/rest/v1/contacts', $payload);
        $contactData = $response->json();

        if (isset($contactData['id'])) {
            $contact_id = $contactData['id'];
            // dd($contact_id);
        }
        // Save
        $survey = Survey::updateOrCreate(
            ['email_address' => $request->email_address],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'telephone_number' => $request->telephone_number,
                'full_address' => $request->full_address,
                'postcode' => $request->postcode,
                'house_or_flat' => $request->house_or_flat,
                'number_of_bedrooms' => $request->number_of_bedrooms,
                'market_value' => $request->market_value,
                'listed_building' => $request->listed_building ?? 'no',
                'over1650' => $request->over1650 ?? 'no',
                'sqft_area' => $request->sqft_area,
                'level1_price' => $level1,
                'level2_price' => $level2,
                'level3_price' => $level3,
                'level4_price' => $level4,
                'level1_payment_url' => $level1_payment_url,
                'level2_payment_url' => $level2_payment_url,
                'level3_payment_url' => $level3_payment_url,
                'level4_payment_url' => $level4_payment_url,
                'current_step' => 0,
                'is_submitted' => false,
                'contact_id' => $contact_id ?? null,
            ]
        );

        $tag_ids = [643];
        $this->apply_tags($contact_id, $tag_ids);

        $key = $this->get_secretbox_key_b64();
        $encrpted_id = $this->encrypt_sodium($survey->contact_id, $key);

        // listing page url
        $redirect_url = "https://flettons.com/flettons-listing/?contact_id={$encrpted_id}&temp=1";

        $updatePayload = [
            'custom_fields' => [
                ['id' => 234, 'content' => $redirect_url],
                ['id' => 601, 'content' => $redirect_url],
            ],
        ];

        $updateResponse = Http::withHeaders([
            'Authorization' => 'Bearer KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
            ->patch("https://api.infusionsoft.com/crm/rest/v1/contacts/{$contact_id}", $updatePayload);

        return redirect()->route('user.flettons.listing.page', ['contact_id' => $encrpted_id, 'temp' => 1]);
    }

    public function flettonsListingPage()
    {
        $encrpted_id = request()->get('contact_id');

        $key = $this->get_secretbox_key_b64();
        $id = $this->decrypt_sodium($encrpted_id, $key);
        $survey = Survey::where('contact_id', $id)->first();

        // ------------- if survey not found -------------
        // dd($encrpted_id);
        if (!$survey) {
            $url = 'https://api.infusionsoft.com/crm/rest/v1/contacts/' . $id . '/?optional_properties=custom_fields';
            $response = Http::withHeaders([
                'X-Keap-API-Key' => 'KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
                'Authorization' => 'Bearer KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
                'Content-Type' => 'application/json',
            ])
                ->patch($url, (object) []);

            // Dump the response to inspect it
            $response_data = $response->json();
            // dd($response_data);
            $data = [];
            $data['first_name'] = $response_data['given_name'] ?? '';
            $data['last_name'] = $response_data['family_name'] ?? '';
            $email_address = $response_data['email_addresses'][0]['email'] ?? '';
            $data['telephone_number'] = $response_data['phone_numbers'][0]['number'] ?? '';
            $data['full_address'] = $response_data['addresses'][0]['line1'] ?? '';
            $data['postcode'] = $response_data['addresses'][0]['postal_code'] ?? '';
            $data['contact_id'] = $id ?? '';

            // show direct data instead of object
            foreach ($response_data['custom_fields'] as $key => $value) {
                if ($value['id'] == 191) {
                    $data['full_address'] = $value['content'];
                }
                if ($value['id'] == 203) {
                    $data['listed_building'] = $value['content'];
                }
                if ($value['id'] == 197) {
                    $data['number_of_bedrooms'] = $value['content'];
                }
                if ($value['id'] == 195) {
                    $data['house_or_flat'] = $value['content'];
                }
                if ($value['id'] == 193) {
                    $data['market_value'] = $value['content'];
                }
                if ($value['id'] == 603) {
                    $data['sqft_area'] = $value['content'];
                    if ($data['sqft_area'] > 1650) {
                        $data['over1650'] = 'yes';
                    } else {
                        $data['over1650'] = 'no';
                    }
                }
                if ($value['id'] == 220) {
                    $data['level1_price'] = $value['content'];
                }
                if ($value['id'] == 224) {
                    $data['level2_price'] = $value['content'];
                }
                if ($value['id'] == 228) {
                    $data['level3_price'] = $value['content'];
                }
                if ($value['id'] == 238) {
                    $data['level4_price'] = $value['content'];
                }
            }

            $survey = Survey::updateOrCreate(
                ['email_address' => $email_address],
                $data
            );

            // dd($survey);
        }

        $price = Price::first();
        $data = [
            'survey' => $survey,
            'price' => $price
        ];

        return view('user.survey.listing', $data);
    }

    public function submitListingPage(Request $request)
    {
        $survey = Survey::where('contact_id', $request->contact_id)->first();
        $key = $this->get_secretbox_key_b64();
        $encrypted_id = $this->encrypt_sodium($survey->contact_id, $key);
        $survey->update([
            'level' => $request->level,
            'level_total' => $request->level_total,
            'breakdown' => $request->breakdown_of_estimated_repair_costs ?? 0,
            'aerial' => $request->aerial_roof_and_chimney ?? 0,
            'insurance' => $request->insurance_reinstatement_valuation ?? 0,
            'addons' => $request->breakdown_of_estimated_repair_costs || $request->aerial_roof_and_chimney || $request->insurance_reinstatement_valuation,
            'level3_price' => ($request->level == 3) ? $request->level_total : $survey->level3_price,
            'current_step' => 1,
            'quote_summary_page' => "https://flettons.com/flettons-summary/?contact_id={$encrypted_id}&temp=1",
        ]);

        // 'quote_summary_page' => route('user.flettons.rics.survey.page', ['contact_id' => $encrypted_id, 'temp' => 1]),
        $key = $this->get_secretbox_key_b64();
        $encrpted_id = $this->encrypt_sodium($survey->contact_id, $key);

        return redirect()->route('user.flettons.rics.survey.page', ['contact_id' => $encrpted_id, 'temp' => 1]);
    }

    public function flettonsRicsSurveyPage()
    {
        $encrpted_id = request()->get('contact_id');
        $key = $this->get_secretbox_key_b64();
        $id = $this->decrypt_sodium($encrpted_id, $key);
        $survey = Survey::where('contact_id', $id)->first();
        $data = [
            'survey' => $survey,
        ];

        return view('user.survey.rics-survey', $data);
    }

    public function submitRicsSurveyPage(Request $request)
    {
        $survey = Survey::findOrFail($request->id);
        $data = $request->all();

        // ✅ Update survey progress
        $data['current_step'] = 2;
        $data['is_submitted'] = true;

        // ✅ Generate payment URLs
        $data['level1_payment_url'] = "https://flettons.group/flettons-order/?email={$survey->email_address}&total={$survey->level1_price}&level=1&order=1";
        $data['level2_payment_url'] = "https://flettons.group/flettons-order/?email={$survey->email_address}&total={$survey->level2_price}&level=2&order=1";
        $data['level3_payment_url'] = "https://flettons.group/flettons-order/?email={$survey->email_address}&total={$survey->level3_price}&level=3&order=1";
        $data['level4_payment_url'] = "https://flettons.group/flettons-order/?email={$survey->email_address}&total={$survey->level4_price}&level=4&order=1";

        $key = $this->get_secretbox_key_b64();
        $encrpted_id = $this->encrypt_sodium($survey->contact_id, $key);
        // Temporary redirect URL back to the RICS survey page (for now)
        // $redirect_url = route('user.flettons.listing.page', ['contact_id' => $encrpted_id, 'temp' => 1]);
        $redirect_url = "https://flettons.com/flettons-listing/?contact_id={$encrpted_id}&temp=1";

        $survey->update($data);

        // ✅ Build CRM payload
        $payload = [
            'given_name' => $survey->first_name,
            'family_name' => $survey->last_name,
            'duplicate_option' => 'Email',
            // Billing address
            'addresses' => [
                [
                    'line1' => $survey->full_address,
                    'locality' => '',
                    'postal_code' => $survey->postcode ?? '',
                    'country_code' => '',
                    'field' => 'BILLING'
                ]
            ],
            // Phone numbers
            'phone_numbers' => [
                [
                    'number' => str_replace(' ', '', $survey->telephone_number),
                    'field' => 'PHONE1'
                ]
            ],
            // Email addresses
            'email_addresses' => [
                [
                    'email' => $survey->email_address,
                    'field' => 'EMAIL1'
                ]
            ],
            // ✅ Custom fields
            'custom_fields' => [
                // Property details
                ['id' => '191', 'content' => $survey->full_address],
                ['id' => '193', 'content' => (int) $survey->market_value],
                ['id' => '195', 'content' => $survey->house_or_flat],
                ['id' => '197', 'content' => (int) $survey->number_of_bedrooms],
                ['id' => '203', 'content' => $survey->listed_building],
                ['id' => '603', 'content' => (int) $survey->sqft_area],
                // Property features
                ['id' => '18', 'content' => $survey->inf_custom_VacantorOccupied],
                ['id' => '14', 'content' => $survey->inf_custom_AnyExtensions],
                ['id' => '10', 'content' => $survey->inf_custom_Garage],
                ['id' => '12', 'content' => $survey->inf_custom_GarageLocation],
                ['id' => '641', 'content' => $survey->inf_custom_Garden],
                ['id' => '639', 'content' => $survey->inf_custom_GardenLocation],
                ['id' => '22', 'content' => $survey->inf_custom_SpecificConcerns],
                // Solicitor details
                ['id' => '579', 'content' => $survey->inf_custom_SolicitorFirmName],
                ['id' => '581', 'content' => $survey->inf_custom_ConveyancerName],
                ['id' => '585', 'content' => str_replace(' ', '', $survey->inf_custom_SolicitorPhoneNumber1)],
                ['id' => '605', 'content' => $survey->inf_custom_SolicitorsEmail],
                ['id' => '589', 'content' => $survey->inf_custom_SolicitorAddress],
                // Exchange timeline
                ['id' => '591', 'content' => $survey->inf_custom_ExchangeDate],
                // Agent details
                ['id' => '24', 'content' => $survey->inf_custom_AgentCompanyName],
                ['id' => '26', 'content' => $survey->inf_custom_AgentName],
                ['id' => '28', 'content' => str_replace(' ', '', $survey->inf_custom_AgentPhoneNumber)],
                ['id' => '165', 'content' => $survey->inf_custom_AgentsEmail],
                // Signature & acceptance
                ['id' => '621', 'content' => $survey->inf_custom_infcustomSignature],
                // Payment links
                ['id' => '218', 'content' => $survey->level1_payment_url],
                ['id' => '222', 'content' => $survey->level2_payment_url],
                ['id' => '226', 'content' => $survey->level3_payment_url],
                ['id' => '240', 'content' => $survey->level4_payment_url],
                // Totals
                ['id' => '220', 'content' => number_format($survey->level1_price, 2)],
                ['id' => '224', 'content' => number_format($survey->level2_price, 2)],
                ['id' => '228', 'content' => number_format($survey->level3_price, 2)],
                ['id' => '238', 'content' => number_format($survey->level4_price, 2)],
                // missing fields
                ['id' => '629', 'content' => $survey->level],
                ['id' => '20', 'content' => $survey->inf_custom_PropertyLink],
                ['id' => '601', 'content' => $survey->quote_summary_page],
                // addons
                ['id' => '208', 'content' => $survey->breakdown],
                ['id' => '210', 'content' => $survey->aerial],
                ['id' => '212', 'content' => $survey->insurance],
                // RedirectURL
                ['id' => '234', 'content' => $redirect_url],
            ]
        ];

        // ✅ Send to Keap
        $response = Http::withHeaders([
            'X-Keap-API-Key' => 'KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
            'Authorization' => 'Bearer KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
            'Content-Type' => 'application/json',
        ])->put('https://api.infusionsoft.com/crm/rest/v1/contacts', $payload);

        $contactData = $response->json();

        if (isset($contactData['id'])) {
            $contact_id = $contactData['id'];
            $tag_ids = [643];
            $this->apply_tags($contact_id, $tag_ids);
        }

        // ✅ Decide the final payment URL based on selected level
        $map = [
            1 => $survey->level1_payment_url,
            2 => $survey->level2_payment_url,
            3 => $survey->level3_payment_url,
            4 => $survey->level4_payment_url,
        ];
        $paymentUrl = $map[(int) $survey->level] ?? null;

        if (!$paymentUrl) {
            return redirect('/')->with('error', 'Invalid level selected.');
        }

        // ✅ Return a tiny HTML/JS page that updates the TOP window (breaks out of iframe)
        $jsSafe = static function (string $value): string {
            return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        };

        $html = <<<HTML
            <!doctype html>
            <html>
            <head>
              <meta charset="utf-8">
              <title>Redirecting…</title>
            </head>
            <body>
            <script>
            (function () {
              var url = {$jsSafe($paymentUrl)};
              try {
                if (window.top && window.top !== window.self) {
                  window.top.location.href = url;   // if inside iframe, update the top address bar
                } else {
                  window.location.replace(url);     // normal redirect if not in iframe
                }
              } catch (e) {
                window.location.href = url;         // fallback
              }
            })();
            </script>
            <noscript>
              <meta http-equiv="refresh" content="0;url={$paymentUrl}">
            </noscript>
            </body>
            </html>
            HTML;

        return response($html, 200)->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function updateSurveyTag($contact_id, $level)
    {
        $tag_ids = [];
        if ($level == 1) {
            $tag_ids = [368];
        } elseif ($level == 2) {
            $tag_ids = [370];
        } elseif ($level == 3) {
            $tag_ids = [372];
        } elseif ($level == 4) {
            $tag_ids = [500];
        }

        $this->apply_tags($contact_id, $tag_ids);
        return response()->json(['success' => true, 'message' => 'Tags applied successfully.']);
    }

    public function apply_tags($contact_id, array $tag_ids = []): bool
    {
        // Keap API URL to apply tags to the contact
        $url = "https://api.infusionsoft.com/crm/rest/v1/contacts/{$contact_id}/tags";

        // Create the payload to apply tags
        $tag_data = [
            'tagIds' => $tag_ids
        ];

        // Send the POST request using Laravel's HTTP client
        $response = Http::withHeaders([
            'X-Keap-API-Key' => 'KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
            'Authorization' => 'Bearer KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
            'Content-Type' => 'application/json',
        ])
            ->post($url, $tag_data);

        // Check if the request was successful
        if ($response->successful()) {
            // Successfully applied the tags
            return true;
        } else {
            // Log the error response for debugging
            return false;
        }
    }

    public function encrypt_sodium(string $plaintext, string $b64key): string
    {
        $key = base64_decode($b64key, true);
        if ($key === false || strlen($key) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RuntimeException('Invalid key');
        }
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = sodium_crypto_secretbox($plaintext, $nonce, $key);
        // Return URL-safe base64 of nonce|cipher
        return $this->b64url_encode($nonce . $cipher);
    }

    /**
     * Decrypt with libsodium secretbox (accepts URL-safe or standard base64)
     */
    public function decrypt_sodium(string $token, string $b64key): string
    {
        $key = base64_decode($b64key, true);
        if ($key === false)
            throw new RuntimeException('Invalid key encoding');

        // Prefer URL-safe decode
        $payload = $this->b64url_decode($token);
        if ($payload === false) {
            // Fallback for legacy standard base64 tokens
            $payload = base64_decode($token, true);
        }
        if ($payload === false)
            throw new RuntimeException('Invalid token base64');

        $nlen = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
        if (strlen($payload) < $nlen)
            throw new RuntimeException('Payload too short');

        $nonce = substr($payload, 0, $nlen);
        $cipher = substr($payload, $nlen);
        $plain = sodium_crypto_secretbox_open($cipher, $nonce, $key);
        if ($plain === false) {
            throw new RuntimeException('Decryption failed (tampered/wrong key)');
        }
        return $plain;
    }

    private function get_secretbox_key_b64(): string
    {
        if (!extension_loaded('sodium')) {
            throw new RuntimeException('The sodium extension is required but not loaded.');
        }

        // Static key: SAME on both sites!
        // You can move this to wp-config.php:
        // define('FLETTONS_SECRETBOX_KEY_B64', 'base64:DQvwZyXGXvbB37lZDViFyM2xjGm88K3NqYg4ROoD9iI=');
        $b64 = defined('FLETTONS_SECRETBOX_KEY_B64')
            ? FLETTONS_SECRETBOX_KEY_B64
            : 'base64:DQvwZyXGXvbB37lZDViFyM2xjGm88K3NqYg4ROoD9iI=';  // fallback inline

        // Accept both "base64:..." and raw base64
        if (strpos($b64, 'base64:') === 0) {
            $b64 = substr($b64, 7);
        }

        $raw = base64_decode($b64, true);
        if ($raw === false || strlen($raw) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RuntimeException('FLETTONS_SECRETBOX_KEY_B64 must be valid base64 of exactly 32 bytes.');
        }

        return $b64;
    }

    private function b64url_decode(string $b64url)
    {
        $b64 = strtr($b64url, '-_', '+/');
        $pad = strlen($b64) % 4;
        if ($pad)
            $b64 .= str_repeat('=', 4 - $pad);
        return base64_decode($b64, true);
    }

    private function b64url_encode(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }
}
