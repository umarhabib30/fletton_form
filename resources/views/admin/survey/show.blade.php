{{-- resources/views/admin/survey/show.blade.php --}}
@extends('layouts.admin')

@section('style')
<style>
    :root {
        --bg: #eef0f5;
        --card: #151b26;
        --lime: #c1ec4a;
        --muted: #6b7280;
        --radius: 10px;
        --shadow: 0 10px 24px rgba(16,24,40,.18);
    }
    .survey-form-card {
        background: var(--card);
        border: 0;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.25rem;
    }
    .survey-form-card .card-header {
        background: rgba(255,255,255,.06);
        border-bottom: 1px solid rgba(255,255,255,.08);
        color: var(--lime);
        font-weight: 600;
        font-size: 14px;
        padding: 12px 18px;
        border-radius: var(--radius) var(--radius) 0 0;
    }
    .survey-form-card .card-body { padding: 18px; }
    .survey-form-label {
        color: var(--lime);
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 6px;
        margin-top: 20px;
        display: block;
    }
    .survey-form-control {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: var(--radius);
        color: #fff;
        padding: 8px 12px;
        font-size: 16px;
        width: 100%;
    }
    .survey-form-control:focus {
        background: rgba(255,255,255,.12);
        border-color: var(--lime);
        color: #fff;
        box-shadow: 0 0 0 2px rgba(193,236,74,.25);
        outline: 0;
    }
    .survey-form-control::placeholder { color: rgba(255,255,255,.4); }
    select.survey-form-control { cursor: pointer; }
    textarea.survey-form-control { min-height: 80px; resize: vertical; }
    .btn-survey-back {
        background: transparent;
        color: var(--lime);
        border: 1px solid rgba(193,236,74,.5);
        border-radius: var(--radius);
        font-weight: 500;
        padding: 8px 16px;
    }
    .btn-survey-back:hover { background: rgba(193,236,74,.15); color: var(--lime); border-color: var(--lime); }
    .btn-survey-update {
        background: var(--lime);
        color: var(--card);
        border: 0;
        border-radius: var(--radius);
        font-weight: 600;
        padding: 10px 24px;
    }
    .btn-survey-update:hover { background: #b5e040; color: var(--card); }
    .survey-badge { font-size: 12px; }
    .survey-readonly { color: rgba(255,255,255,.6); font-size: 14px; }

    /* Survey header / detail section */
    .survey-header-card {
        background: var(--card);
        border: 0;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.25rem;
        padding: 18px 22px;
    }
    .survey-header-card .survey-page-title {
        color: var(--lime);
        font-size: 20px;
        font-weight: 600;
        margin: 0 0 16px 0;
    }
    .survey-info-row {
        display: flex;
        flex-wrap: wrap;
        gap: 24px 32px;
        align-items: center;
    }
    .survey-info-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .survey-info-item .survey-info-label {
        color: rgba(255,255,255,.6);
        font-size: 13px;
        font-weight: 500;
    }
    .survey-info-item .survey-info-value {
        color: var(--lime);
        font-size: 14px;
        font-weight: 600;
    }
    .survey-info-item .survey-info-value.badge-submitted {
        background: rgba(34, 197, 94, .2);
        color: #22c55e;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
    }
    .survey-info-item .survey-info-value.badge-progress {
        background: rgba(234, 179, 8, .2);
        color: #eab308;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
    }
</style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if (!$survey)
                <div class="alert alert-warning">Survey not found.</div>
            @else
                @php $s = $survey; @endphp

                {{-- Detail Survey header card --}}
                <div class="survey-header-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h1 class="survey-page-title mb-0">{{ $table ?? 'Detail Survey' }}</h1>
                        <a href="{{ route('admin.survey.index') }}" class="btn btn-survey-back">← Back to list</a>
                    </div>
                    <div class="survey-info-row mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,.08);">
                        <div class="survey-info-item">
                            <span class="survey-info-label">ID</span>
                            <span class="survey-info-value">{{ $s->id }}</span>
                        </div>
                        <div class="survey-info-item">
                            <span class="survey-info-label">Step</span>
                            <span class="survey-info-value">{{ $s->current_step }}</span>
                        </div>
                        <div class="survey-info-item">
                            <span class="survey-info-label">Status</span>
                            @if($s->is_submitted)
                                <span class="survey-info-value badge-submitted">Submitted</span>
                            @else
                                <span class="survey-info-value badge-progress">In Progress</span>
                            @endif
                        </div>
                    </div>
                </div>

                <form method="post" action="{{ route('admin.survey.update', $s->id) }}">
                    @csrf

                    {{-- Step 0: Quote form --}}
                    <div class="survey-form-card card">
                        <div class="card-header">Step 0 — Quote form (initial details)</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="survey-form-label">First name</label><input type="text" name="first_name" class="survey-form-control form-control" value="{{ old('first_name', $s->first_name) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Last name</label><input type="text" name="last_name" class="survey-form-control form-control" value="{{ old('last_name', $s->last_name) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Email</label><input type="email" name="email_address" class="survey-form-control form-control" value="{{ old('email_address', $s->email_address) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Telephone</label><input type="text" name="telephone_number" class="survey-form-control form-control" value="{{ old('telephone_number', $s->telephone_number) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Full address</label><input type="text" name="full_address" class="survey-form-control form-control" value="{{ old('full_address', $s->full_address) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Postcode</label><input type="text" name="postcode" class="survey-form-control form-control" value="{{ old('postcode', $s->postcode) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">House or flat</label><input type="text" name="house_or_flat" class="survey-form-control form-control" value="{{ old('house_or_flat', $s->house_or_flat) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Number of bedrooms</label><input type="number" name="number_of_bedrooms" class="survey-form-control form-control" value="{{ old('number_of_bedrooms', $s->number_of_bedrooms) }}" min="0"></div>
                                <div class="col-md-6"><label class="survey-form-label">Market value (GBP)</label><input type="number" name="market_value" class="survey-form-control form-control" value="{{ old('market_value', $s->market_value) }}" min="0" step="1"></div>
                                <div class="col-md-6"><label class="survey-form-label">Listed building</label><input type="text" name="listed_building" class="survey-form-control form-control" value="{{ old('listed_building', $s->listed_building) }}" placeholder="yes / no"></div>
                                <div class="col-md-6"><label class="survey-form-label">Over 1650 sqft</label><input type="text" name="over1650" class="survey-form-control form-control" value="{{ old('over1650', $s->over1650) }}" placeholder="yes / no"></div>
                                <div class="col-md-6"><label class="survey-form-label">Area (sqft)</label><input type="number" name="sqft_area" class="survey-form-control form-control" value="{{ old('sqft_area', $s->sqft_area) }}" min="0"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 1: Package selection --}}
                    <div class="survey-form-card card">
                        <div class="card-header">Step 1 — Package selection</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="survey-form-label">Selected Level</label><input type="text" name="level" class="survey-form-control form-control" value="{{ old('level', $s->level) }}" placeholder="1, 2, 3 or 4"></div>
                                <div class="col-md-6"><label class="survey-form-label">Level total</label><input type="number" name="level_total" class="survey-form-control form-control" value="{{ old('level_total', $s->level_total) }}" min="0" step="0.01"></div>
                                <div class="col-md-6"><label class="survey-form-label">Level 1 price</label><input type="number" name="level1_price" class="survey-form-control form-control" value="{{ old('level1_price', $s->level1_price) }}" min="0" step="0.01"></div>
                                <div class="col-md-6"><label class="survey-form-label">Level 2 price</label><input type="number" name="level2_price" class="survey-form-control form-control" value="{{ old('level2_price', $s->level2_price) }}" min="0" step="0.01"></div>
                                <div class="col-md-6"><label class="survey-form-label">Level 3 price</label><input type="number" name="level3_price" class="survey-form-control form-control" value="{{ old('level3_price', $s->level3_price) }}" min="0" step="0.01"></div>
                                <div class="col-md-6"><label class="survey-form-label">Level 4 price</label><input type="number" name="level4_price" class="survey-form-control form-control" value="{{ old('level4_price', $s->level4_price) }}" min="0" step="0.01"></div>
                                <div class="col-12"><label class="survey-form-label">Quote summary page URL</label><input type="url" name="quote_summary_page" class="survey-form-control form-control" value="{{ old('quote_summary_page', $s->quote_summary_page) }}" placeholder="https://"></div>
                                <div class="col-md-6"><label class="survey-form-label">Breakdown</label><select name="breakdown" class="survey-form-control form-control"><option value="">—</option><option value="1" {{ old('breakdown', $s->breakdown) == 1 ? 'selected' : '' }}>Yes</option><option value="0" {{ old('breakdown', $s->breakdown) == 0 && $s->breakdown !== null ? 'selected' : '' }}>No</option></select></div>
                                <div class="col-md-6"><label class="survey-form-label">Aerial</label><select name="aerial" class="survey-form-control form-control"><option value="">—</option><option value="1" {{ old('aerial', $s->aerial) == 1 ? 'selected' : '' }}>Yes</option><option value="0" {{ old('aerial', $s->aerial) == 0 && $s->aerial !== null ? 'selected' : '' }}>No</option></select></div>
                                <div class="col-md-6"><label class="survey-form-label">Insurance</label><select name="insurance" class="survey-form-control form-control"><option value="">—</option><option value="1" {{ old('insurance', $s->insurance) == 1 ? 'selected' : '' }}>Yes</option><option value="0" {{ old('insurance', $s->insurance) == 0 && $s->insurance !== null ? 'selected' : '' }}>No</option></select></div>
                                <div class="col-md-6"><label class="survey-form-label">Addons</label><select name="addons" class="survey-form-control form-control"><option value="">—</option><option value="1" {{ old('addons', $s->addons) == 1 ? 'selected' : '' }}>Yes</option><option value="0" {{ old('addons', $s->addons) == 0 && $s->addons !== null ? 'selected' : '' }}>No</option></select></div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3A: Client identity --}}
                    <div class="survey-form-card card">
                        <div class="card-header">Step 3A — Client identity & contact</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="survey-form-label">Title</label><input type="text" name="inf_field_Title" class="survey-form-control form-control" value="{{ old('inf_field_Title', $s->inf_field_Title) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">First name</label><input type="text" name="inf_field_FirstName" class="survey-form-control form-control" value="{{ old('inf_field_FirstName', $s->inf_field_FirstName) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Last name</label><input type="text" name="inf_field_LastName" class="survey-form-control form-control" value="{{ old('inf_field_LastName', $s->inf_field_LastName) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Email</label><input type="email" name="inf_field_Email" class="survey-form-control form-control" value="{{ old('inf_field_Email', $s->inf_field_Email) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Phone</label><input type="text" name="inf_field_Phone1" class="survey-form-control form-control" value="{{ old('inf_field_Phone1', $s->inf_field_Phone1) }}"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3B: Billing address --}}
                    <div class="survey-form-card card">
                        <div class="card-header">Step 3B — Home/Billing address</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="survey-form-label">Street Address 1</label><input type="text" name="inf_field_StreetAddress1" class="survey-form-control form-control" value="{{ old('inf_field_StreetAddress1', $s->inf_field_StreetAddress1) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Postal Code</label><input type="text" name="inf_field_PostalCode" class="survey-form-control form-control" value="{{ old('inf_field_PostalCode', $s->inf_field_PostalCode) }}"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3C: Survey property address --}}
                    <div class="survey-form-card card">
                        <div class="card-header">Step 3C — Survey property address</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="survey-form-label">Address2 Street1</label><input type="text" name="inf_field_Address2Street1" class="survey-form-control form-control" value="{{ old('inf_field_Address2Street1', $s->inf_field_Address2Street1) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Postal Code 2</label><input type="text" name="inf_field_PostalCode2" class="survey-form-control form-control" value="{{ old('inf_field_PostalCode2', $s->inf_field_PostalCode2) }}"></div>
                                <div class="col-12"><label class="survey-form-label">Property Link</label><input type="url" name="inf_custom_PropertyLink" class="survey-form-control form-control" value="{{ old('inf_custom_PropertyLink', $s->inf_custom_PropertyLink) }}" placeholder="https://"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3E: Property features --}}
                    <div class="survey-form-card card">
                        <div class="card-header">Step 3E — Property features & concerns</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="survey-form-label">Vacant or Occupied</label><input type="text" name="inf_custom_VacantorOccupied" class="survey-form-control form-control" value="{{ old('inf_custom_VacantorOccupied', $s->inf_custom_VacantorOccupied) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Any Extensions</label><input type="text" name="inf_custom_AnyExtensions" class="survey-form-control form-control" value="{{ old('inf_custom_AnyExtensions', $s->inf_custom_AnyExtensions) }}" placeholder="yes / no"></div>
                                <div class="col-md-6"><label class="survey-form-label">Garage</label><input type="text" name="inf_custom_Garage" class="survey-form-control form-control" value="{{ old('inf_custom_Garage', $s->inf_custom_Garage) }}" placeholder="yes / no"></div>
                                <div class="col-md-6"><label class="survey-form-label">Garage Location</label><input type="text" name="inf_custom_GarageLocation" class="survey-form-control form-control" value="{{ old('inf_custom_GarageLocation', $s->inf_custom_GarageLocation) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Garden</label><input type="text" name="inf_custom_Garden" class="survey-form-control form-control" value="{{ old('inf_custom_Garden', $s->inf_custom_Garden) }}" placeholder="yes / no"></div>
                                <div class="col-md-6"><label class="survey-form-label">Garden Location</label><input type="text" name="inf_custom_GardenLocation" class="survey-form-control form-control" value="{{ old('inf_custom_GardenLocation', $s->inf_custom_GardenLocation) }}"></div>
                                <div class="col-12"><label class="survey-form-label">Specific Concerns</label><textarea name="inf_custom_SpecificConcerns" class="survey-form-control form-control" rows="3">{{ old('inf_custom_SpecificConcerns', $s->inf_custom_SpecificConcerns) }}</textarea></div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3F: Solicitor --}}
                    <div class="survey-form-card card">
                        <div class="card-header">Step 3F — Solicitor details</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="survey-form-label">Solicitor Firm</label><input type="text" name="inf_custom_SolicitorFirm" class="survey-form-control form-control" value="{{ old('inf_custom_SolicitorFirm', $s->inf_custom_SolicitorFirm) }}" placeholder="yes / no"></div>
                                <div class="col-md-6"><label class="survey-form-label">Firm Name</label><input type="text" name="inf_custom_SolicitorFirmName" class="survey-form-control form-control" value="{{ old('inf_custom_SolicitorFirmName', $s->inf_custom_SolicitorFirmName) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Conveyancer Name</label><input type="text" name="inf_custom_ConveyancerName" class="survey-form-control form-control" value="{{ old('inf_custom_ConveyancerName', $s->inf_custom_ConveyancerName) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Phone</label><input type="text" name="inf_custom_SolicitorPhoneNumber1" class="survey-form-control form-control" value="{{ old('inf_custom_SolicitorPhoneNumber1', $s->inf_custom_SolicitorPhoneNumber1) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Email</label><input type="email" name="inf_custom_SolicitorsEmail" class="survey-form-control form-control" value="{{ old('inf_custom_SolicitorsEmail', $s->inf_custom_SolicitorsEmail) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Address</label><input type="text" name="inf_custom_SolicitorAddress" class="survey-form-control form-control" value="{{ old('inf_custom_SolicitorAddress', $s->inf_custom_SolicitorAddress) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Postal Code</label><input type="text" name="inf_custom_SolicitorPostalCode" class="survey-form-control form-control" value="{{ old('inf_custom_SolicitorPostalCode', $s->inf_custom_SolicitorPostalCode) }}"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3G: Exchange --}}
                    <div class="survey-form-card card">
                        <div class="card-header">Step 3G — Exchange timeline</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="survey-form-label">Exchange date known?</label><input type="text" name="inf_custom_exchange_known" class="survey-form-control form-control" value="{{ old('inf_custom_exchange_known', $s->inf_custom_exchange_known) }}" placeholder="yes / no"></div>
                                <div class="col-md-6"><label class="survey-form-label">Exchange Date</label><input type="text" name="inf_custom_ExchangeDate" class="survey-form-control form-control" value="{{ old('inf_custom_ExchangeDate', $s->inf_custom_ExchangeDate) }}"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3H: Agent --}}
                    <div class="survey-form-card card">
                        <div class="card-header">Step 3H — Estate agent details</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="survey-form-label">Company</label><input type="text" name="inf_custom_AgentCompanyName" class="survey-form-control form-control" value="{{ old('inf_custom_AgentCompanyName', $s->inf_custom_AgentCompanyName) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Agent Name</label><input type="text" name="inf_custom_AgentName" class="survey-form-control form-control" value="{{ old('inf_custom_AgentName', $s->inf_custom_AgentName) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Phone</label><input type="text" name="inf_custom_AgentPhoneNumber" class="survey-form-control form-control" value="{{ old('inf_custom_AgentPhoneNumber', $s->inf_custom_AgentPhoneNumber) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Email</label><input type="email" name="inf_custom_AgentsEmail" class="survey-form-control form-control" value="{{ old('inf_custom_AgentsEmail', $s->inf_custom_AgentsEmail) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Address3 Street1</label><input type="text" name="inf_field_Address3Street1" class="survey-form-control form-control" value="{{ old('inf_field_Address3Street1', $s->inf_field_Address3Street1) }}"></div>
                                <div class="col-md-6"><label class="survey-form-label">Postal Code 3</label><input type="text" name="inf_field_PostalCode3" class="survey-form-control form-control" value="{{ old('inf_field_PostalCode3', $s->inf_field_PostalCode3) }}"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3I & Tracking --}}
                    <div class="survey-form-card card">
                        <div class="card-header">Step 3I & Tracking</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="survey-form-label">Terms accepted</label><input type="text" name="inf_option_IconfirmthatIhavereadandunderstandtheterms" class="survey-form-control form-control" value="{{ old('inf_option_IconfirmthatIhavereadandunderstandtheterms', $s->inf_option_IconfirmthatIhavereadandunderstandtheterms) }}" placeholder="yes / no"></div>
                                <div class="col-md-6"><label class="survey-form-label">Current step</label><input type="number" name="current_step" class="survey-form-control form-control" value="{{ old('current_step', $s->current_step) }}" min="0" max="3"></div>
                                <div class="col-md-6"><label class="survey-form-label">Is submitted</label><select name="is_submitted" class="survey-form-control form-control"><option value="0" {{ old('is_submitted', $s->is_submitted) == 0 ? 'selected' : '' }}>No</option><option value="1" {{ old('is_submitted', $s->is_submitted) == 1 ? 'selected' : '' }}>Yes</option></select></div>
                                <div class="col-md-6"><label class="survey-form-label">Contact ID (Keap)</label><input type="text" name="contact_id" class="survey-form-control form-control" value="{{ old('contact_id', $s->contact_id) }}" readonly></div>
                                <div class="col-12"><label class="survey-form-label">Signature (URL)</label><input type="text" name="inf_custom_infcustomSignature" class="survey-form-control form-control" value="{{ old('inf_custom_infcustomSignature', $s->inf_custom_infcustomSignature) }}" placeholder="Image URL"></div>
                                @if($s->inf_custom_infcustomSignature && filter_var($s->inf_custom_infcustomSignature, FILTER_VALIDATE_URL))
                                    <div class="col-12"><label class="survey-form-label">Signature preview</label><div><img src="{{ $s->inf_custom_infcustomSignature }}" alt="Signature" style="max-height:80px; border-radius: var(--radius);"></div></div>
                                @endif
                            </div>
                            <hr class="border-secondary my-3">
                            <div class="row">
                                <div class="col-md-6"><span class="survey-form-label">Created at</span><div class="survey-readonly">{{ $s->created_at ? \Carbon\Carbon::parse($s->created_at)->format('d M Y H:i') : '—' }}</div></div>
                                <div class="col-md-6"><span class="survey-form-label">Updated at</span><div class="survey-readonly">{{ $s->updated_at ? \Carbon\Carbon::parse($s->updated_at)->format('d M Y H:i') : '—' }}</div></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <a href="{{ route('admin.survey.index') }}" class="btn btn-survey-back">← Back to list</a>
                        <button type="submit" class="btn btn-survey-update">Update Survey</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
