<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [

    // =========================
    // signup form (initial details)
    // =========================
    'first_name',
    'last_name',
    'email_address',
    'telephone_number',
    'full_address',
    'postcode',
    'house_or_flat',
    'number_of_bedrooms',        // (quote page)
    'market_value',
    'listed_building',
    'over1650',
    'sqft_area',

    // ============================================
    // listing page calculated fields --- IGNORE --
    // ============================================
    'level',
    'breakdown',
    'aerial',
    'insurance',
    'level_total',
    'addons',


    // =========================
    // STEP 3A: Client identity & contact
    // =========================
    'inf_field_Title',
    'inf_field_FirstName',
    'inf_field_LastName',
    'inf_field_Email',
    'inf_field_Phone1',

    // =========================
    // STEP 3B: Home/billing address (client)
    // =========================
    'inf_field_StreetAddress1',
    'inf_field_PostalCode',

    // =========================
    // STEP 3C: Survey property address
    // =========================
    'inf_field_Address2Street1',
    'inf_field_PostalCode2',
    'inf_custom_PropertyLink',


    // =========================
    // STEP 3E: Property features & concerns
    // =========================
    'inf_custom_VacantorOccupied',
    'inf_custom_AnyExtensions',
    'inf_custom_Garage',
    'inf_custom_GarageLocation',
    'inf_custom_Garden',
    'inf_custom_GardenLocation',
    'inf_custom_SpecificConcerns',

    // =========================
    // STEP 3F: Solicitor details
    // =========================
    'inf_custom_SolicitorFirm',
    'inf_custom_SolicitorFirmName',
    'inf_custom_SolicitorPhoneNumber1',
    'inf_custom_SolicitorsEmail',
    'inf_custom_SolicitorAddress',
    'inf_custom_SolicitorPostalCode',

    // =========================
    // STEP 3G: Exchange timeline
    // =========================
    'inf_custom_exchange_known',
    'inf_custom_ExchangeDate',

    // =========================
    // STEP 3H: Estate agent details
    // =========================
    'inf_custom_AgentCompanyName',
    'inf_custom_AgentName',
    'inf_custom_AgentPhoneNumber',
    'inf_custom_AgentsEmail',
    'inf_field_Address3Street1',
    'inf_field_PostalCode3',


    'level1_price',
    'level2_price',
    'level3_price',
    'level4_price',

    'level1_payment_url',
    'level2_payment_url',
    'level3_payment_url',
    'level4_payment_url',

    // =========================
    // STEP 3I: Acceptance & signature
    // =========================
    'inf_option_IconfirmthatIhavereadandunderstandtheterms',
    'inf_custom_infcustomSignature',

    'current_step',
    'is_submitted',
];

}
