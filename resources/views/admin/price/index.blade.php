@extends('layouts.admin')

@section('content')
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="basicform">
            <h3 class="section-title">Price Table</h3>
            <p>Set base amounts, market percentages, and other costs.</p>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.price.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Base amounts -->
                        <div class="form-group col-md-6">
                            <label for="level1_base">Level 1 Base</label>
                            <input id="level1_base" name="level1_base" type="number" step="0.01" min="0"
                                class="form-control" value="{{ @$price->level1_base }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="level2_base">Level 2 Base</label>
                            <input id="level2_base" name="level2_base" type="number" step="0.01" min="0"
                                class="form-control" value="{{ @$price->level2_base }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="level3_base">Level 3 Base</label>
                            <input id="level3_base" name="level3_base" type="number" step="0.01" min="0"
                                class="form-control" value="{{ @$price->level3_base }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="level4_base">Level 4 Base</label>
                            <input id="level4_base" name="level4_base" type="number" step="0.01" min="0"
                                class="form-control" value="{{ @$price->level4_base }}">
                        </div>

                        <!-- Market percentages -->
                        <div class="form-group col-md-6">
                            <label for="level1_market_percentage">Level 1 Market %</label>
                            <input id="level1_market_percentage" name="level1_market_percentage" type="number"
                                step="0.01"  class="form-control"
                                value="{{ @$price->level1_market_percentage }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="level2_market_percentage">Level 2 Market %</label>
                            <input id="level2_market_percentage" name="level2_market_percentage" type="number"
                                step="0.01"  class="form-control"
                                value="{{ @$price->level2_market_percentage }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="level3_market_percentage">Level 3 Market %</label>
                            <input id="level3_market_percentage" name="level3_market_percentage" type="number"
                                step="0.01"  class="form-control"
                                value="{{ @$price->level3_market_percentage }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="level4_market_percentage">Level 4 Market %</label>
                            <input id="level4_market_percentage" name="level4_market_percentage" type="number"
                                step="0.01"  class="form-control"
                                value="{{ @$price->level4_market_percentage }}">
                        </div>

                        <!-- Other costs -->
                        <div class="form-group col-md-6">
                            <label for="repair_cost">Repair Cost</label>
                            <input id="repair_cost" name="repair_cost" type="number" step="0.01" min="0"
                                class="form-control" value="{{ @$price->repair_cost }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="aerial_chimney_cost">Aerial Chimney Cost</label>
                            <input id="aerial_chimney_cost" name="aerial_chimney_cost" type="number" step="0.01"
                                min="0" class="form-control" value="{{ @$price->aerial_chimney_cost }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="insurance_cost">Insurance Cost</label>
                            <input id="insurance_cost" name="insurance_cost" type="number" step="0.01"
                                min="0" class="form-control" value="{{ @$price->insurance_cost }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="thermal_image_cost">Thermal Image Cost</label>
                            <input id="thermal_image_cost" name="thermal_image_cost" type="number" step="0.01"
                                min="0" class="form-control" value="{{ @$price->thermal_image_cost }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="listing_cost">Listing Cost</label>
                            <input id="listing_cost" name="listing_cost" type="number" step="0.01" min="0"
                                class="form-control" value="{{ @$price->listing_cost }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="extra_sqft_cost">Extra Sqft Cost</label>
                            <input id="extra_sqft_cost" name="extra_sqft_cost" type="number" step="0.01"
                                min="0" class="form-control" value="{{ @$price->extra_sqft_cost }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="extra_reception_room_cost">Extra Reception Room Cost</label>
                            <input id="extra_reception_room_cost" name="extra_reception_room_cost" type="number"
                                step="0.01" min="0" class="form-control"
                                value="{{ @$price->extra_reception_room_cost }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="extra_room_cost">Extra Room Cost</label>
                            <input id="extra_room_cost" name="extra_room_cost" type="number" step="0.01"
                                min="0" class="form-control" value="{{ @$price->extra_room_cost }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
