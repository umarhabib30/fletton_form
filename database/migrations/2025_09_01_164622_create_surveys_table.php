<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            // =========================
            // STEP 0: Quote form (initial details)
            // =========================
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email_address')->nullable()->index();
            $table->string('telephone_number')->nullable();
            $table->string('full_address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('house_or_flat')->nullable();
            $table->unsignedSmallInteger('number_of_bedrooms')->nullable();
            $table->unsignedBigInteger('market_value')->nullable();           // stored as whole pounds
            $table->string('listed_building')->nullable();                    // 0/1
            $table->string('over1650')->nullable();                           // 0/1
            $table->unsignedInteger('sqft_area')->nullable();


            // =========================
            // listing page calculated fields
            // =========================
            $table->string('level')->nullable();
            $table->boolean('breakdown')->default(false);
            $table->boolean('aerial')->default(false);
            $table->boolean('insurance')->default(false);
            $table->decimal('level_total', 8, 2)->nullable();
            $table->boolean('addons')->default(false);

            // =========================
            // STEP 3A: Client identity & contact
            // =========================
            $table->string('inf_field_Title')->nullable();
            $table->string('inf_field_FirstName')->nullable();
            $table->string('inf_field_LastName')->nullable();
            $table->string('inf_field_Email')->nullable()->index();
            $table->string('inf_field_Phone1')->nullable();

            // =========================
            // STEP 3B: Home/billing address (client)
            // =========================
            $table->string('inf_field_StreetAddress1')->nullable();
            $table->string('inf_field_PostalCode')->nullable();

            // =========================
            // STEP 3C: Survey property address
            // =========================
            $table->string('inf_field_Address2Street1')->nullable();
            $table->string('inf_field_PostalCode2')->nullable();
            $table->string('inf_custom_PropertyLink')->nullable();

            // =========================
            // STEP 3E: Property features & concerns
            // =========================
            $table->string('inf_custom_VacantorOccupied')->nullable(); // 'Vacant' / 'Occupied'
            $table->string('inf_custom_AnyExtensions')->nullable();   // 0/1
            $table->string('inf_custom_Garage')->nullable();          // 0/1
            $table->string('inf_custom_GarageLocation')->nullable();
            $table->string('inf_custom_Garden')->nullable();          // 0/1
            $table->string('inf_custom_GardenLocation')->nullable();
            $table->text('inf_custom_SpecificConcerns')->nullable();

            // =========================
            // STEP 3F: Solicitor details
            // =========================
            $table->string('inf_custom_SolicitorFirm')->nullable();        // 'yes'/'no' if radio
            $table->string('inf_custom_SolicitorFirmName')->nullable();
            $table->string('inf_custom_ConveyancerName')->nullable();
            $table->string('inf_custom_SolicitorPhoneNumber1')->nullable();
            $table->string('inf_custom_SolicitorsEmail')->nullable();
            $table->string('inf_custom_SolicitorAddress')->nullable();
            $table->string('inf_custom_SolicitorPostalCode')->nullable();

            // =========================
            // STEP 3G: Exchange timeline
            // =========================
            $table->string('inf_custom_exchange_known')->nullable();       // 'yes'/'no'
            $table->string('inf_custom_ExchangeDate')->nullable();         // keep as string if free-text

            // =========================
            // STEP 3H: Estate agent details
            // =========================
            $table->string('inf_custom_AgentCompanyName')->nullable();
            $table->string('inf_custom_AgentName')->nullable();
            $table->string('inf_custom_AgentPhoneNumber')->nullable();
            $table->string('inf_custom_AgentsEmail')->nullable();
            $table->string('inf_field_Address3Street1')->nullable();
            $table->string('inf_field_PostalCode3')->nullable();

            // =========================
            // STEP 3I: Acceptance & signature
            // =========================
            $table->string('inf_option_IconfirmthatIhavereadandunderstandtheterms')->nullable(); // checkbox
            $table->text('inf_custom_infcustomSignature')->nullable();                             // blob/JSON/plain



            $table->decimal('level1_price', 10, 2)->nullable();
            $table->decimal('level2_price', 10, 2)->nullable();
            $table->decimal('level3_price', 10, 2)->nullable();
            $table->decimal('level4_price', 10, 2)->nullable();

            $table->string('level1_payment_url')->nullable();
            $table->string('level2_payment_url')->nullable();
            $table->string('level3_payment_url')->nullable();
            $table->string('level4_payment_url')->nullable();


            // =========================
            // Tracking fields (requested)
            // =========================
            $table->unsignedTinyInteger('current_step')->default(0)->comment('0=signup,1=listings,2=Rics survey');
            $table->string('is_submitted')->default(false)->comment('true when the whole form is completed');

            $table->string('contact_id')->nullable();

            $table->string('quote_summary_page')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
