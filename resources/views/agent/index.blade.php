@extends('layouts.admin.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">
            {{ $data['name'] ?? ($data['customer'] ?? null) }} Agents List
          </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            @isset($data['name'])
              <li class="breadcrumb-item"><a href="{{ route('office.index') }}">Office</a></li>
            @endisset
            <li class="breadcrumb-item active">Agents</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <input type="hidden" id="office_id" name="office_id" @isset($_GET['office']) value="{{ $_GET['office'] }}" @endisset>
          <input type="hidden" id="customer_id" name="customer_id" @isset($_GET['customer']) value="{{ $_GET['customer'] }}" @endisset>
          <div class="table-overlay bg-theme">
            <h5 class="text-light m-0"><i class="fas fa-sync fa-spin mr-2"></i>Please wait....</h5>
          </div>
          {{-- <a href="#" class="nav-link" id="table-state">Clear current search query, filters, and sorts</a> --}}
          <div class="card">
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-hover">
                <thead>
                  <tr>
                    @if (Auth::user()->role == 'office')
                      <th>Image</th>
                    @endif
                    <th>Name</th>
                    @if (Auth::user()->role !== 'office')
                      <th>Customer</th>
                      <th>Office</th>
                    @endif
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
@endsection
@push('script')
  <script>
    var table = $('#example1').DataTable({
      responsive: true,
      autoWidth: false,
      pageLength: 25,
      dom: '<"toolbar">frtip',
      lengthChange: false,
      "order": [
        [0, 'asc']
      ],
      stateSave: true,
      serverSide: true,
      ajax: {
        url: "{{ route('agent.index') }}",
        type: "GET",
        data: function(d) {
          d.office = $('#office_id').val();
          d.customer = $('#customer_id').val();
        }
      },
      columns: [
        @if (Auth::user()->role == 'office')
          {
            data: 'profile_photo',
            name: 'user.profile_photo'
          },
        @endif {
          data: 'name',
          name: 'name'
        },
        @if (Auth::user()->role !== 'office')
          {
            data: 'cname',
            name: 'customer.name'
          }, {
            data: 'office',
            name: 'office.name',
          },
        @endif {
          data: 'email',
          name: 'user.email'
        },
        {
          data: 'phone',
          name: 'phone'
        },
        {
          data: 'status',
          name: 'user.status',
          serachable: false
        },
        {
          data: 'action',
          orderable: false,
          serachable: false
        },
      ]
    });

    $('div.toolbar').html('<a href="{{ route('agent.create') }}" class="btn btn-theme btn-flat"><i class="fa-light fa-plus"></i> Add New Agent</a>');

    $(document).ajaxComplete(function() {
      $('html, body').animate({
        scrollTop: 0
      }, 'slow');
    });
  </script>
@endpush
