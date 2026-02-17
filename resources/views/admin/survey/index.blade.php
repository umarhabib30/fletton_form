@extends('layouts.admin')
@section('content')
    <div class="row">
        <!-- ============================================================== -->
        <!-- basic table  -->
        <!-- ============================================================== -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <h5 class="card-header" style="font-size: 22px !important; font-weight: 600 !important; color: #1b202b !important;"> {{ $table }}</h5>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered first">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Survey Level</th>
                                    <th>Address</th>
                                    <th>Form Stage</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                          <tbody>
                              @foreach ($surveys as $survey)
                                  <tr>
                                      <td>{{ $survey->first_name }} {{ $survey->last_name }}</td>
                                      <td>{{ $survey->email_address }}</td>
                                      <td>{{ $survey->telephone_number }}</td>
                                      <td>{{ $survey->level }}</td>
                                      <td>{{ $survey->full_address }}</td>
                                      <td>{{ $survey->current_step }}</td>
                                      <td><a href="{{ route('admin.survey.show', $survey->id) }}" class="btn btn-primary">Details</a></td>
                                  </tr>
                              @endforeach
                          </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- end basic table  -->
        <!-- ============================================================== -->
    </div>
@endsection
