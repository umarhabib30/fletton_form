    @extends('layouts.admin')
    @section('style')
        <style>
           label{
            font-size: 15px !important;
            font-weight: 600 !important;
            color: #1b202b !important;

           }
        </style>
    @endsection
    @section('content')
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

            <div class="card">
                <h5 class="card-header" style="font-size: 22px !important; font-weight: 600 !important; color: #1b202b !important;">Product Pricing</h5>
                <div class="card-body">
                    <form action="{{ route('admin.price.update') }}" method="POST">
                        @csrf
                        <style>
                            .price-section{
                                border: 1px solid #e9ecef;
                                border-radius: 12px;
                                padding: 18px 18px 10px;
                                margin-bottom: 18px;
                                background: #fff;
                            }
                            .price-section__title{
                                font-size: 16px;
                                font-weight: 700;
                                color: #1b202b;
                                margin-bottom: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                            }
                            .price-section__divider{
                                height: 1px;
                                background: #eef1f5;
                                margin: 10px 0 16px;
                            }
                            .form-label{
                                font-size: 15px !important;
                                font-weight: 600 !important;
                                color: #1b202b !important;
                                margin-bottom: 6px;
                                margin-top: 20px;
                            }
                            /* helps perfect alignment if some browsers render inputs slightly different */
                            .form-control{
                                height: 44px;
                                border-radius: 10px;
                            }
                        </style>

                        <div class="row">
                            <div class="col-12">

                                {{-- BASE AMOUNTS --}}
                                <div class="price-section">
                                    <div class="price-section__title">
                                        <span>Base Prices  </span>
                                    </div>
                                    <div class="price-section__divider"></div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="level1_base">Level 1 </label>
                                            <input id="level1_base" name="level1_base" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->level1_base }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="level2_base">Level 2 </label>
                                            <input id="level2_base" name="level2_base" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->level2_base }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="level3_base">Level 3 </label>
                                            <input id="level3_base" name="level3_base" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->level3_base }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="level4_base">Level 4 </label>
                                            <input id="level4_base" name="level4_base" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->level4_base }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- MARKET PERCENTAGES --}}
                                <div class="price-section">
                                    <div class="price-section__title">
                                        <span>Percentage Uplift %</span>
                                    </div>
                                    <div class="price-section__divider"></div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="level1_market_percentage">Level 1 %</label>
                                            <input id="level1_market_percentage" name="level1_market_percentage" type="number" step="any"
                                                   class="form-control" value="{{ @$price->level1_market_percentage }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="level2_market_percentage">Level 2 %</label>
                                            <input id="level2_market_percentage" name="level2_market_percentage" type="number" step="any"
                                                   class="form-control" value="{{ @$price->level2_market_percentage }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="level3_market_percentage">Level 3 %</label>
                                            <input id="level3_market_percentage" name="level3_market_percentage" type="number" step="any"
                                                   class="form-control" value="{{ @$price->level3_market_percentage }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="level4_market_percentage">Level 4 %</label>
                                            <input id="level4_market_percentage" name="level4_market_percentage" type="number" step="any"
                                                   class="form-control" value="{{ @$price->level4_market_percentage }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- OTHER COSTS --}}
                                <div class="price-section">
                                    <div class="price-section__title">
                                        <span>Add-on and Other Pricing</span>
                                    </div>
                                    <div class="price-section__divider"></div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="repair_cost">Breakdown of Estimated Costs (£).</label>
                                            <input id="repair_cost" name="repair_cost" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->repair_cost }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="aerial_chimney_cost">Aerial Drone Cost (£), </label>
                                            <input id="aerial_chimney_cost" name="aerial_chimney_cost" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->aerial_chimney_cost }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="insurance_cost">Reinstatement Cost (£)</label>
                                            <input id="insurance_cost" name="insurance_cost" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->insurance_cost }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="thermal_image_cost">Thermal Imaging Cost (£)</label>
                                            <input id="thermal_image_cost" name="thermal_image_cost" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->thermal_image_cost }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="listing_cost">Listed Building Cost (£) </label>
                                            <input id="listing_cost" name="listing_cost" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->listing_cost }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="extra_sqft_cost">Excess SqFt Cost (£)</label>
                                            <input id="extra_sqft_cost" name="extra_sqft_cost" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->extra_sqft_cost }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="extra_reception_room_cost">Extra Reception Room Cost (£)</label>
                                            <input id="extra_reception_room_cost" name="extra_reception_room_cost" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->extra_reception_room_cost }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="extra_room_cost">Extra Room Cost (£)</label>
                                            <input id="extra_room_cost" name="extra_room_cost" type="number" step="any" min="0"
                                                   class="form-control" value="{{ @$price->extra_room_cost }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-primary px-4 " style="border-radius: 4px !important;">Update</button>
                                </div>

                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endsection
