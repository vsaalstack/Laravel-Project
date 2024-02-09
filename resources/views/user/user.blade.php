@extends('layouts.admin.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"> Admin Management </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">User</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <!-- /.card-header -->
            <div class="card-body table-responsive-sm">
              <table id="example1" class="table table-hover">
                <thead>
                  <tr>
                    <th>User Name</th>
                    <th>Email Id</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Created Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($users as $user)
                    <tr>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->email }}</td>
                      <td>{{ $user->status == '0' ? 'Deactive' : 'Active' }}</td>
                      <td>{{ ucfirst($user->role) }}</td>
                      <td>{{ date('Y-m-d', strtotime($user->created_at)) }}</td>
                      <td>
                        <div class="btn-group">
                          <a title="Edit" href="{{ route('user.edit',[$user->id]) }}" class="btn btn-sm px-1 "><i class="icon-Button-Edit"></i></a>
                          {{-- <a title="Switch User" href="{{route('switch.user',[$user->id])}}" class="btn btn-sm px-1"><i class="fa-thin fa-people-arrows fa-lg"></i></a> --}}
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>

    </div>

  </section>
  <!-- /.content -->
@endsection

@push('script')
  <script>
    $('#example1').DataTable({
      responsive: true,
      autoWidth: false,
      pageLength: 25,
      dom: '<"toolbar">frtip',
      lengthChange: false,
      order: [],

    });
    $('div.toolbar').html('<a href="{{ route('user.create') }}" class="btn btn-theme btn-flat"><i class="fa-light fa-plus"></i> Add New Admin</a>');
  </script>
@endpush
