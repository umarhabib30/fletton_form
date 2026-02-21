@extends('layouts.admin')
@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
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
                                    <th>Actions</th>
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
                                      <td>
                                          <a href="{{ route('admin.survey.show', $survey->id) }}" class="btn btn-primary btn-sm">Details</a>
                                          <button type="button" class="btn btn-danger btn-sm btn-delete-survey" data-url="{{ route('admin.survey.delete', $survey->id) }}" data-name="{{ $survey->first_name }} {{ $survey->last_name }}">Delete</button>
                                      </td>
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

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-delete-survey').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const url = this.getAttribute('data-url');
        const name = this.getAttribute('data-name') || 'this survey';
        Swal.fire({
            title: 'Delete record?',
            html: 'You are about to delete the survey for <strong>' + name + '</strong>. This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>
@endsection
