<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WebHookController extends Controller
{
    public function updateKeapContactFromId()
    {
        $contact_id =$_REQUEST['id'];
        // 1. Fetch contact from Keap
        $response = Http::withHeaders([
            'Authorization' => 'Bearer KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
            'Accept' => 'application/json',
        ])->get("https://api.infusionsoft.com/crm/rest/v1/contacts/{$contact_id}?optional_properties=custom_fields");

        $contact = $response->json();

        if (!isset($contact['id'])) {
            throw new \Exception('Contact not found');
        }

        // 2. Load Pricing Settings
        $settings = Price::first();

        // --- Retrieve Custom Field Values ---
        $marketValue = (float) $this->getFieldValue($contact['custom_fields'], 193);
        $sqft = (int) $this->getFieldValue($contact['custom_fields'], 603);
        $listed = $this->getFieldValue($contact['custom_fields'], 203);
        $bedrooms = (int) $this->getFieldValue($contact['custom_fields'], 197);

        // --- Cost Calculations (same logic you used before) ---
        $listedCost = $listed == 'yes' ? $settings->listing_cost : 0;
        $sqftCost = ($sqft > 1650) ? ($sqft - 1650) * $settings->extra_sqft_cost : 0;
        $bedroomCost = ($bedrooms > 4) ? ($bedrooms - 4) * $settings->extra_room_cost : 0;

        $additionalCost = $listedCost + $sqftCost + $bedroomCost;

        $level1 = $settings->level1_base + ($marketValue * $settings->level1_market_percentage) + $bedroomCost + $listedCost;
        $level2 = $settings->level2_base + ($marketValue * $settings->level2_market_percentage) + $additionalCost;
        $level3 = $settings->level3_base + ($marketValue * $settings->level3_market_percentage) + $additionalCost;
        $level4 = $settings->level4_base + ($marketValue * $settings->level4_market_percentage) + $additionalCost;

        $email = $contact['email_addresses'][0]['email'];

        // 3. Build payment URLs
        $level1_payment_url = "https://flettons.group/flettons-order/?email={$email}&total={$level1}&level=1&order=1";
        $level2_payment_url = "https://flettons.group/flettons-order/?email={$email}&total={$level2}&level=2&order=1";
        $level3_payment_url = "https://flettons.group/flettons-order/?email={$email}&total={$level3}&level=3&order=1";
        $level4_payment_url = "https://flettons.group/flettons-order/?email={$email}&total={$level4}&level=4&order=1";

        // 4. Encrypt ID for listing URL
        $key = $this->get_secretbox_key_b64();
        $encrypted_id = $this->encrypt_sodium($contact_id, $key);
        $listing_url = "https://flettons.com/flettons-listing/?contact_id={$encrypted_id}&temp=1";

        // 5. Build update payload
        $updatePayload = [
            'custom_fields' => [
                ['id' => 220, 'content' => number_format($level1, 2)],
                ['id' => 224, 'content' => number_format($level2, 2)],
                ['id' => 228, 'content' => number_format($level3, 2)],
                ['id' => 238, 'content' => number_format($level4, 2)],
                ['id' => 218, 'content' => $level1_payment_url],
                ['id' => 222, 'content' => $level2_payment_url],
                ['id' => 226, 'content' => $level3_payment_url],
                ['id' => 240, 'content' => $level4_payment_url],
                ['id' => 234, 'content' => $listing_url],
                ['id' => 601, 'content' => $listing_url],
            ],
        ];

        // 6. Update contact
        Http::withHeaders([
            'Authorization' => 'Bearer YOUR_KEAP_API_KEY',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->patch("https://api.infusionsoft.com/crm/rest/v1/contacts/{$contact_id}", $updatePayload);

        return [
            'contact_id' => $contact_id,
            'listing_url' => $listing_url,
            'level_prices' => compact('level1', 'level2', 'level3', 'level4')
        ];
    }

    /**
     * Helper to extract Keap custom field value
     */
    private function getFieldValue($customFields, $id)
    {
        foreach ($customFields as $field) {
            if ($field['id'] == $id) {
                return $field['content'];
            }
        }
        return null;
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
