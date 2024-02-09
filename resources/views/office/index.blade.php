@extends('layouts.admin.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">
            @isset($name)
              {{ $name }}
            @endisset Offices List
          </h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            @isset($name)
              <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Customer</a></li>
            @endisset
            <li class="breadcrumb-item active">Office</li>
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
          <div class="table-overlay bg-theme">
            <h5 class="text-light m-0"><i class="fas fa-sync fa-spin mr-2"></i>Please wait....</h5>
          </div>
          <div class="card">
            <!-- /.card-header -->
            <div class="card-body table-responsive-sm">
              <table id="example1" class="table table-hover">
                <thead>
                  <tr>
                    <th>Office Name</th>
                    <th>Customer</th>
                    <th>Apify Task</th>
                    <th>Apify Status</th>
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
        url: "{{ route('office.index') }}",
        type: 'GET',
        data: function(d) {
          d.id = "{{ Request::segment(3) }}";
        }
      },
      columns: [{
          data: 'name',
          name: 'name'
        },
        {
          data: 'cname',
          name: 'customer.name'
        },
        {
          data: 'apify',
          name: 'apify_task_id',
        },
        {
          data: 'status',
          name: 'status'
        },
        {
          data: 'action',
          name: 'action',
          orderable: false,
          serachable: false
        },
      ]
    });

    $('div.toolbar').html('<a href="{{ route('office.create') }}" class="btn btn-theme btn-flat"><i class="fa-light fa-plus"></i> Add New Office</a>');

    $(document).ajaxComplete(function() {
      $('html, body').animate({
        scrollTop: 0
      }, 'slow');
    });

    $(document).ready(function() {
      $('body').on('click', '.deleteOffice', function() {
        Swal.fire({
          title: 'Are you sure ?',
          text: "Want to delete this office with user shared history?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3398C2',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            var url = $(this).data('url');
            $.ajax({
              type: "Delete",
              url: url,
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              success: function(response) {
                if (response) {
                  Toast.fire({
                    icon: response.status,
                    title: response.msg,
                  })
                  location.reload();
                }
              }
            });
          }
        })
      });
    });
  </script>
@endpush
