<?php

namespace Database\Seeders;

use App\Models\Survey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Base record with correct types
        $base = [
            'first_name' => 'John',
                'last_name' => 'Doe',
                'email_address' => 'john@example.com',
                'telephone_number' => '07123456789',
                'full_address' => '123 Main Street',
                'postcode' => 'AB12CD',
                'house_or_flat' => 'House',
                'number_of_bedrooms' => 3,
                'market_value' => 250000,
                'listed_building' => false,
                'over1650' => false,
                'sqft_area' => 1200,
                'action' => 'quote',
                'quote_form_nonce' => 'abc123',
                'level3' => 590.00,
                'level4' => 1100.00,
                'inf_custom_selectedlevel' => 'level3',
                'breakdown_of_estimated_repair_costs' => true,
                'aerial_roof_and_chimney' => false,
                'insurance_reinstatement_valuation' => true,
                'inf_form_xid' => 'form001',
                'inf_form_name' => 'Quote Form',
                'infusionsoft_version' => '1.0',
                'inf_field_Title' => 'Mr',
                'inf_field_FirstName' => 'John',
                'inf_field_LastName' => 'Doe',
                'inf_field_Email' => 'john@example.com',
                'inf_field_Phone1' => '07123456789',
                'inf_field_StreetAddress1' => '123 Main Street',
                'inf_field_PostalCode' => 'AB12CD',
                'inf_field_Address2Street1' => 'Property Lane',
                'inf_field_PostalCode2' => 'XY98ZZ',
                'inf_custom_PropertyLink' => 'http://example.com/property/1',
                'inf_custom_PropertyType' => 'Detached',
                'inf_custom_NumberofBedrooms' => 3,
                'inf_custom_VacantorOccupied' => 'Occupied',
                'inf_custom_AnyExtensions' => true,
                'inf_custom_Garage' => true,
                'inf_custom_GarageLocation' => 'Side',
                'inf_custom_Garden' => true,
                'inf_custom_GardenLocation' => 'Rear',
                'inf_custom_SpecificConcerns' => 'Cracks in walls',
                'inf_custom_SolicitorFirm' => 'Yes',
                'inf_custom_SolicitorFirmName' => 'Law & Co',
                'inf_custom_SolicitorPhoneNumber1' => '07000000001',
                'inf_custom_SolicitorsEmail' => 'solicitor@example.com',
                'inf_custom_SolicitorAddress' => '45 Legal Street',
                'inf_custom_SolicitorPostalCode' => 'XY99ZZ',
                'inf_custom_exchange_known' => 'Yes',
                'inf_custom_ExchangeDate' => '2025-10-01',
                'inf_custom_AgentCompanyName' => 'Estate Experts',
                'inf_custom_AgentName' => 'Alice Smith',
                'inf_custom_AgentPhoneNumber' => '07000000002',
                'inf_custom_AgentsEmail' => 'agent@example.com',
                'inf_field_Address3Street1' => '45 Agency Road',
                'inf_field_PostalCode3' => 'AB45CD',
                'inf_option_IconfirmthatIhavereadandunderstandtheterms' => true,
                'inf_custom_infcustomSignature' => 'Signed by John Doe',
                'current_step' => 3,
                'is_submitted' => true,
        ];

        // Create 5 records with small variations
        for ($i = 0; $i < 5; $i++) {
            $data = $base;
            $data['email_address'] = 'john' . ($i + 1) . '@example.com';
            $data['inf_field_FirstName'] = 'John' . ($i + 1);
            $data['inf_field_LastName'] = 'Doe' . ($i + 1);
            $data['inf_field_Email'] = 'john' . ($i + 1) . '@example.com';
            $data['inf_custom_PropertyLink'] = 'http://example.com/property/' . ($i + 1);
            $data['inf_custom_SolicitorFirmName'] = 'Law & Co ' . ($i + 1);
            $data['inf_custom_ExchangeDate'] = date('Y-m-d', strtotime("+$i days"));
            Survey::create($data);
        }
    }
}
