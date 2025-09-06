{{-- resources/views/admin/surveys/show.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-3">
      <h5 class="card-header d-flex justify-content-between align-items-center">
        {{ $table ?? 'Detail Survey' }}
        <a href="{{ route('admin.survey.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
      </h5>
      <div class="card-body pt-3">
        @php
          $s = $survey ?? null;

          $boolBadge = fn($v) => is_null($v)
            ? '<span class="badge bg-secondary">N/A</span>'
            : ($v ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>');

          $money = fn($v) => is_null($v) || $v==='' ? '—' : '£'.number_format((float)$v, 2);
          $int   = fn($v) => is_null($v) || $v==='' ? '—' : number_format((int)$v);
          $str   = fn($v) => $v ?: '—';
          $dt    = fn($v) => $v ? \Carbon\Carbon::parse($v)->format('d M Y H:i') : '—';

          $mailto = fn($v)=>$v ? '<a href="mailto:'.e($v).'">'.e($v).'</a>' : '—';
          $tel    = fn($v)=>$v ? '<a href="tel:'.preg_replace('/\s+/', '', e($v)).'">'.e($v).'</a>' : '—';
          $url    = function($v){
            if(!$v) return '—';
            $u = str_starts_with($v,'http') ? $v : "https://$v";
            return filter_var($u, FILTER_VALIDATE_URL)
              ? '<a href="'.e($u).'" target="_blank" rel="noopener">'.e($v).'</a>'
              : e($v);
          };
        @endphp

        @if(!$s)
          <div class="alert alert-warning mb-0">Survey not found.</div>
        @else
          <div class="mb-3">
            <span class="badge badge-primary">ID: {{ $s->id }}</span>
            <span class="badge badge-info">Step: {{ $s->current_step }}</span>
            {!! $s->is_submitted ? '<span class="badge badge-success">Submitted</span>' : '<span class="badge bg-warning text-dark">In Progress</span>' !!}
          </div>

          {{-- STEP 0 --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 0 — Quote form (initial details)</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">First name</th><td>{{ $str($s->first_name) }}</td></tr>
                    <tr><th scope="row">Last name</th><td>{{ $str($s->last_name) }}</td></tr>
                    <tr><th scope="row">Email</th><td>{!! $mailto($s->email_address) !!}</td></tr>
                    <tr><th scope="row">Telephone</th><td>{!! $tel($s->telephone_number) !!}</td></tr>
                    <tr><th scope="row">Full address</th><td>{{ $str($s->full_address) }}</td></tr>
                    <tr><th scope="row">Postcode</th><td>{{ $str($s->postcode) }}</td></tr>
                    <tr><th scope="row">House or flat</th><td>{{ $str($s->house_or_flat) }}</td></tr>
                    <tr><th scope="row">Bedrooms</th><td>{{ $int($s->number_of_bedrooms) }}</td></tr>
                    <tr><th scope="row">Market value (GBP)</th><td>{{ $money($s->market_value) }}</td></tr>
                    <tr><th scope="row">Listed building</th><td>{!! $boolBadge($s->listed_building) !!}</td></tr>
                    <tr><th scope="row">Built over 1650</th><td>{!! $boolBadge($s->over1650) !!}</td></tr>
                    <tr><th scope="row">Area (sqft)</th><td>{{ $int($s->sqft_area) }}</td></tr>
                    <tr><th scope="row">Action</th><td>{{ $str($s->action) }}</td></tr>
                    <tr><th scope="row">Quote form nonce</th><td><code>{{ $str($s->quote_form_nonce) }}</code></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 1 --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 1 — Package selection (levels)</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Level 3 base price</th><td>£{{ number_format($s->level3 ?? 0, 2) }}</td></tr>
                    <tr><th scope="row">Level 4 base price</th><td>£{{ number_format($s->level4 ?? 0, 2) }}</td></tr>
                    <tr><th scope="row">Selected level</th><td>{{ $str($s->inf_custom_selectedlevel) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 2 --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 2 — Level 3 add-ons</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Breakdown of estimated repair costs</th><td>{!! $boolBadge($s->breakdown_of_estimated_repair_costs) !!}</td></tr>
                    <tr><th scope="row">Aerial roof & chimney</th><td>{!! $boolBadge($s->aerial_roof_and_chimney) !!}</td></tr>
                    <tr><th scope="row">Insurance reinstatement valuation</th><td>{!! $boolBadge($s->insurance_reinstatement_valuation) !!}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 3 META --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 3 — Customer signup (meta)</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Inf Form XID</th><td>{{ $str($s->inf_form_xid) }}</td></tr>
                    <tr><th scope="row">Inf Form Name</th><td>{{ $str($s->inf_form_name) }}</td></tr>
                    <tr><th scope="row">Infusionsoft Version</th><td>{{ $str($s->infusionsoft_version) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 3A --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 3A — Client identity & contact</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Title</th><td>{{ $str($s->inf_field_Title) }}</td></tr>
                    <tr><th scope="row">First name</th><td>{{ $str($s->inf_field_FirstName) }}</td></tr>
                    <tr><th scope="row">Last name</th><td>{{ $str($s->inf_field_LastName) }}</td></tr>
                    <tr><th scope="row">Email</th><td>{!! $mailto($s->inf_field_Email) !!}</td></tr>
                    <tr><th scope="row">Phone</th><td>{!! $tel($s->inf_field_Phone1) !!}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 3B --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 3B — Home/Billing address</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Street Address 1</th><td>{{ $str($s->inf_field_StreetAddress1) }}</td></tr>
                    <tr><th scope="row">Postal Code</th><td>{{ $str($s->inf_field_PostalCode) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 3C --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 3C — Survey property address</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Address2 Street1</th><td>{{ $str($s->inf_field_Address2Street1) }}</td></tr>
                    <tr><th scope="row">Postal Code 2</th><td>{{ $str($s->inf_field_PostalCode2) }}</td></tr>
                    <tr><th scope="row">Property Link</th><td>{!! $url($s->inf_custom_PropertyLink) !!}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 3D --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 3D — Property profile</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Property Type</th><td>{{ $str($s->inf_custom_PropertyType) }}</td></tr>
                    <tr><th scope="row">Bedrooms</th><td>{{ $int($s->inf_custom_NumberofBedrooms) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 3E --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 3E — Property features & concerns</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Vacant or Occupied</th><td>{{ $str($s->inf_custom_VacantorOccupied) }}</td></tr>
                    <tr><th scope="row">Any Extensions</th><td>{!! $boolBadge($s->inf_custom_AnyExtensions) !!}</td></tr>
                    <tr><th scope="row">Garage</th><td>{!! $boolBadge($s->inf_custom_Garage) !!}</td></tr>
                    <tr><th scope="row">Garage Location</th><td>{{ $str($s->inf_custom_GarageLocation) }}</td></tr>
                    <tr><th scope="row">Garden</th><td>{!! $boolBadge($s->inf_custom_Garden) !!}</td></tr>
                    <tr><th scope="row">Garden Location</th><td>{{ $str($s->inf_custom_GardenLocation) }}</td></tr>
                    <tr><th scope="row">Specific Concerns</th><td style="white-space:pre-wrap">{{ $str($s->inf_custom_SpecificConcerns) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 3F --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 3F — Solicitor details</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Has Solicitor?</th><td>{{ $str($s->inf_custom_SolicitorFirm) }}</td></tr>
                    <tr><th scope="row">Firm Name</th><td>{{ $str($s->inf_custom_SolicitorFirmName) }}</td></tr>
                    <tr><th scope="row">Phone</th><td>{{ $str($s->inf_custom_SolicitorPhoneNumber1) }}</td></tr>
                    <tr><th scope="row">Email</th><td>{{ $str($s->inf_custom_SolicitorsEmail) }}</td></tr>
                    <tr><th scope="row">Address</th><td>{{ $str($s->inf_custom_SolicitorAddress) }}</td></tr>
                    <tr><th scope="row">Postal Code</th><td>{{ $str($s->inf_custom_SolicitorPostalCode) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 3G --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 3G — Exchange timeline</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Exchange date known?</th><td>{{ $str($s->inf_custom_exchange_known) }}</td></tr>
                    <tr><th scope="row">Exchange Date</th><td>{{ $str($s->inf_custom_ExchangeDate) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 3H --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 3H — Estate agent details</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Company</th><td>{{ $str($s->inf_custom_AgentCompanyName) }}</td></tr>
                    <tr><th scope="row">Agent Name</th><td>{{ $str($s->inf_custom_AgentName) }}</td></tr>
                    <tr><th scope="row">Phone</th><td>{{ $str($s->inf_custom_AgentPhoneNumber) }}</td></tr>
                    <tr><th scope="row">Email</th><td>{{ $str($s->inf_custom_AgentsEmail) }}</td></tr>
                    <tr><th scope="row">Address3 Street1</th><td>{{ $str($s->inf_field_Address3Street1) }}</td></tr>
                    <tr><th scope="row">Postal Code 3</th><td>{{ $str($s->inf_field_PostalCode3) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- STEP 3I --}}
          <div class="card mb-3">
            <h6 class="card-header">Step 3I — Acceptance & signature</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Terms accepted</th><td>{!! $boolBadge($s->inf_option_IconfirmthatIhavereadandunderstandtheterms) !!}</td></tr>
                    <tr><th scope="row">Signature</th><td><img src="{{ $s->inf_custom_infcustomSignature }}" alt=""></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- TRACKING --}}
          <div class="card">
            <h6 class="card-header">Tracking & Timestamps</h6>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-bordered first mb-0">
                  <tbody>
                    <tr><th scope="row" style="width:280px">Current Step</th><td>{{ $s->current_step }}</td></tr>
                    <tr><th scope="row">Is Submitted</th>
                      <td>{!! $s->is_submitted ? '<span class="badge badge-success">Yes</span>' : '<span class="badge bg-warning text-dark">No</span>' !!}</td>
                    </tr>
                    <tr><th scope="row">Created at</th><td>{{ $dt($s->created_at) }}</td></tr>
                    <tr><th scope="row">Updated at</th><td>{{ $dt($s->updated_at) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        @endif
      </div>
    </div>
  </div>
</div>
@endsection
