<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Survey;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

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
                'current_step' => 0,
                'is_submitted' => false,

                'level1_price' => $level1,
                'level2_price' => $level2,
                'level3_price' => $level3,
                'level4_price' => $level4,
            ]
        );

        return redirect()->route('user.flettons.listing.page', $survey->id);
    }



    public function flettonsListingPage($id)
    {
        $survey = Survey::find($id);
        $price = Price::first();
        $data = [
            'survey' => $survey,
            'price' => $price
        ];

        return view('user.survey.listing', $data);
    }

    public function submitListingPage(Request $request)
    {

        $survey = Survey::find($request->id);
        $survey->update([
            'level' => $request->level,
            'level_total' => $request->level_total,
            'breakdown' => $request->breakdown_of_estimated_repair_costs,
            'aerial' => $request->aerial_roof_and_chimney,
            'insurance' => $request->insurance_reinstatement_valuation,
            'addons' => $request->breakdown_of_estimated_repair_costs || $request->aerial_roof_and_chimney || $request->insurance_reinstatement_valuation,
            'level3_price' => ($request->level == 3) ? $request->level_total : $survey->level3_price,
            'current_step' => 1,
        ]);

        return redirect()->route('user.flettons.rics.survey.page', $survey->id);
    }


    public function flettonsRicsSurveyPage($id)
    {
        $survey = Survey::find($id);
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

        $survey->update($data);

        // ✅ Build CRM payload
        $payload = [
            "given_name" => $survey->first_name,
            "family_name" => $survey->last_name,
            "duplicate_option" => "Email",

            // Billing address
            "addresses" => [
                [
                    "line1" => $survey->full_address,
                    "line2" => "",
                    "locality" => "",
                    "postal_code" => $survey->postcode ?? "",
                    "country_code" => "",
                    "field" => "BILLING"
                ]
            ],

            // Phone numbers
            "phone_numbers" => [
                [
                    "number" => $survey->telephone_number,
                    "field" => "PHONE1"
                ]
            ],

            // Email addresses
            "email_addresses" => [
                [
                    "email" => $survey->email_address,
                    "field" => "EMAIL1"
                ]
            ],

            // ✅ Custom fields
            "custom_fields" => [

                // Property details
                ["id" => "191", "content" => $survey->full_address],
                ["id" => "193", "content" => (int) $survey->market_value],
                ["id" => "195", "content" => $survey->house_or_flat],
                ["id" => "197", "content" => (int) $survey->number_of_bedrooms],
                ["id" => "203", "content" => $survey->listed_building],
                ["id" => "603", "content" => (int) $survey->sqft_area],

                // Property features
                ["id" => "18", "content" => $survey->inf_custom_VacantorOccupied],
                ["id" => "14", "content" => $survey->inf_custom_AnyExtensions],
                ["id" => "10", "content" => $survey->inf_custom_Garage],
                ["id" => "12", "content" => $survey->inf_custom_GarageLocation],
                ["id" => "641", "content" => $survey->inf_custom_Garden],
                ["id" => "639", "content" => $survey->inf_custom_GardenLocation],
                ["id" => "22", "content" => $survey->inf_custom_SpecificConcerns],

                // Solicitor details
                ["id" => "579", "content" => $survey->inf_custom_SolicitorFirmName],
                ["id" => "585", "content" => $survey->inf_custom_SolicitorPhoneNumber1],
                ["id" => "605", "content" => $survey->inf_custom_SolicitorsEmail],
                ["id" => "589", "content" => $survey->inf_custom_SolicitorAddress],

                // Exchange timeline
                ["id" => "591", "content" => $survey->inf_custom_ExchangeDate],

                // Agent details
                ["id" => "24", "content" => $survey->inf_custom_AgentCompanyName],
                ["id" => "26", "content" => $survey->inf_custom_AgentName],
                ["id" => "28", "content" => $survey->inf_custom_AgentPhoneNumber],
                ["id" => "165", "content" => $survey->inf_custom_AgentsEmail],

                // Signature & acceptance
                ["id" => "621", "content" => $survey->inf_custom_infcustomSignature],

                // Payment links
                ["id" => "218", "content" => $survey->level1_payment_url],
                ["id" => "222", "content" => $survey->level2_payment_url],
                ["id" => "226", "content" => $survey->level3_payment_url],
                ["id" => "240", "content" => $survey->level4_payment_url],

                // Totals
                ["id" => "220", "content" => number_format($survey->level1_price, 2)],
                ["id" => "224", "content" => number_format($survey->level2_price, 2)],
                ["id" => "228", "content" => number_format($survey->level3_price, 2)],
                ["id" => "238", "content" => number_format($survey->level4_price, 2)],
            ]
        ];

        // ✅ Send to Keap
        $response = Http::withHeaders([
            'X-Keap-API-Key' => 'KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
            'Authorization' => 'Bearer KeapAK-6348cc09f8ed9b4800c6cb2ed4e0f9473ba5d9c249bb465acf',
            'Content-Type' => 'application/json',
        ])->put('https://api.infusionsoft.com/crm/rest/v1/contacts', $payload);

        

        switch ($survey->level) {
            case 1:
                return redirect()->away($survey->level1_payment_url);
            case 2:
                return redirect()->away($survey->level2_payment_url);
            case 3:
                return redirect()->away($survey->level3_payment_url);
            case 4:
                return redirect()->away($survey->level4_payment_url);
            default:
                return redirect('/')->with('error', 'Invalid level selected.');
        }
    }
}
