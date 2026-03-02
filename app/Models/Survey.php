<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Survey model – stores all steps of the Flettons survey and syncs with Keap CRM.
 *
 * Keap CRM mapping:
 * ─────────────────────────────────────────────────────────────────────────
 * Contact (standard):  given_name, family_name, email_addresses[0].email,
 *                     phone_numbers[0].number, addresses[0].line1/postal_code
 * Custom fields (ID):  191=address line, 193=market value, 195=house or flat,
 *                     197=bedrooms, 203=listed building, 603=sqft,
 *                     220/224/228/238=level 1–4 prices, 218/222/226/240=payment URLs,
 *                     234/601=redirect URL, 18=Vacant/Occupied, 14=Extensions,
 *                     10/12=Garage, 641/639=Garden, 22=Specific concerns,
 *                     579/581/585/605/589=Solicitor, 591=Exchange date,
 *                     24/26/28/165=Agent, 621=Signature, 629=selected level,
 *                     20=Property link, 208/210/212=addons, 665=Title
 * ─────────────────────────────────────────────────────────────────────────
 * This model exposes Keap names via accessors: given_name, family_name,
 * primary_email, primary_phone, address_line1, postal_code (map to DB columns).
 */
class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        // Keap contact aliases (stored in DB columns below via mutators)
        'given_name',
        'family_name',
        'primary_email',
        'primary_phone',
        'address_line1',
        'postal_code',

        // Step 0: Quote form (Keap contact + custom)
        'first_name',
        'last_name',
        'email_address',
        'telephone_number',
        'full_address',
        'postcode',
        'house_or_flat',           // Keap custom 195
        'number_of_bedrooms',      // Keap custom 197
        'market_value',            // Keap custom 193
        'listed_building',         // Keap custom 203
        'over1650',
        'sqft_area',               // Keap custom 603

        // Step 1: Listing / package selection
        'level',                   // Keap custom 629
        'breakdown',               // Keap custom 208
        'aerial',                  // Keap custom 210
        'insurance',               // Keap custom 212
        'level_total',
        'addons',
        'level1_price',            // Keap custom 220
        'level2_price',            // Keap custom 224
        'level3_price',            // Keap custom 228
        'level4_price',            // Keap custom 238
        'level1_payment_url',      // Keap custom 218
        'level2_payment_url',      // Keap custom 222
        'level3_payment_url',      // Keap custom 226
        'level4_payment_url',      // Keap custom 240

        // Step 3A: Client identity (Keap contact)
        'inf_field_Title',         // Keap custom 665
        'inf_field_FirstName',
        'inf_field_LastName',
        'inf_field_Email',
        'inf_field_Phone1',

        // Step 3B: Billing address
        'inf_field_StreetAddress1',
        'inf_field_StreetAddress2',
        'inf_field_PostalCode',

        // Step 3C: Survey property address
        'inf_field_Address2Street1',
        'inf_field_PostalCode2',
        'inf_custom_PropertyLink', // Keap custom 20

        // Step 3E: Property features
        'inf_custom_VacantorOccupied',  // Keap 18
        'inf_custom_AnyExtensions',     // Keap 14
        'inf_custom_Garage',            // Keap 10
        'inf_custom_GarageLocation',    // Keap 12
        'inf_custom_Garden',            // Keap 641
        'inf_custom_GardenLocation',    // Keap 639
        'inf_custom_SpecificConcerns',  // Keap 22

        // Step 3F: Solicitor
        'inf_custom_SolicitorFirm',
        'inf_custom_SolicitorFirmName',   // Keap 579
        'inf_custom_ConveyancerName',    // Keap 581
        'inf_custom_SolicitorPhoneNumber1', // Keap 585
        'inf_custom_SolicitorsEmail',     // Keap 605
        'inf_custom_SolicitorAddress',    // Keap 589
        'inf_custom_SolicitorPostalCode',

        // Step 3G: Exchange
        'inf_custom_exchange_known',
        'inf_custom_ExchangeDate',       // Keap 591

        // Step 3H: Agent
        'inf_custom_AgentCompanyName',   // Keap 24
        'inf_custom_AgentName',          // Keap 26
        'inf_custom_AgentPhoneNumber',   // Keap 28
        'inf_custom_AgentsEmail',        // Keap 165
        'inf_field_Address3Street1',
        'inf_field_PostalCode3',

        // Step 3I
        'inf_option_IconfirmthatIhavereadandunderstandtheterms',
        'inf_custom_infcustomSignature',  // Keap 621

        'contact_id',
        'quote_summary_page',       // Keap 601
        'current_step',
        'is_submitted',
    ];

    /**
     * Keap contact field: given_name (maps to first_name).
     */
    public function getGivenNameAttribute(): ?string
    {
        return $this->attributes['first_name'] ?? null;
    }

    public function setGivenNameAttribute($value): void
    {
        $this->attributes['first_name'] = $value;
    }

    /**
     * Keap contact field: family_name (maps to last_name).
     */
    public function getFamilyNameAttribute(): ?string
    {
        return $this->attributes['last_name'] ?? null;
    }

    public function setFamilyNameAttribute($value): void
    {
        $this->attributes['last_name'] = $value;
    }

    /**
     * Keap primary email (maps to email_address).
     */
    public function getPrimaryEmailAttribute(): ?string
    {
        return $this->attributes['email_address'] ?? null;
    }

    public function setPrimaryEmailAttribute($value): void
    {
        $this->attributes['email_address'] = $value;
    }

    /**
     * Keap primary phone (maps to telephone_number).
     */
    public function getPrimaryPhoneAttribute(): ?string
    {
        return $this->attributes['telephone_number'] ?? null;
    }

    public function setPrimaryPhoneAttribute($value): void
    {
        $this->attributes['telephone_number'] = $value;
    }

    /**
     * Keap address line1 (maps to full_address).
     */
    public function getAddressLine1Attribute(): ?string
    {
        return $this->attributes['full_address'] ?? null;
    }

    public function setAddressLine1Attribute($value): void
    {
        $this->attributes['full_address'] = $value;
    }

    /**
     * Keap postal_code (maps to postcode).
     */
    public function getPostalCodeAttribute(): ?string
    {
        return $this->attributes['postcode'] ?? null;
    }

    public function setPostalCodeAttribute($value): void
    {
        $this->attributes['postcode'] = $value;
    }
}
