@extends('layouts.admin.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Create Agent</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <a href="{{ url()->previous() }}" class="btn btn-theme btn-flat"><i class="fa-light fa-arrow-left"></i> Back</a>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <form action="{{ route('agent.store') }}" method="post" enctype="multipart/form-data">@csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Customer</label>
                      <select class="form-control @error('customer_id') is-invalid @enderror select2-customer" style="width: 100%;" name="customer_id" id="customer_id" required placeholder="Select Customer">
                        <option value="" selected disabled></option>
                        @foreach ($customers as $customer)
                          <option value="{{ $customer->id }}" @selected($customer->id == old('customer_id'))>{{ $customer->name }}</option>
                        @endforeach
                      </select>
                      @error('customer_id')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Office</label>
                      @php $office_id = empty(old('office_id')) ? 0 : implode(',',old('office_id'))  @endphp
                      <input type="hidden" value="{{ $office_id }}" id="old_office_id">
                      <select class="form-control @error('office_id') is-invalid @enderror select2" style="width: 100%;" name="office_id[]" id="office_id" required multiple placeholder="Select offices">

                      </select>
                      @error('office_id')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Agent Name</label>
                      <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name" name="name" value="{{ old('name') }}" required>
                      @error('name')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="phone">Phone</label>
                      <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" placeholder="Enter phone" name="phone" value="{{ old('phone') }}">
                      @error('phone')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="email">Email</label>
                      <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter Email" name="email" value="{{ old('email') }}">
                      @error('email')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="instagram_handle">Instagram Handle</label>
                      <input type="text" class="form-control" id="instagram_handle" placeholder="Instagram Handle" name="instagram_handle" value="{{ old('name') }}">
                    </div>
                  </div>
                  @if (in_array(Auth::user()->role,['admin','super-admin']))
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Status</label>
                        <div class="form-group clearfix">
                          <div class="icheck-info d-inline mr-2">
                            <input type="radio" id="active" name="status" value="1" @checked(old('status') == 1 )>
                            <label for="active">Active</label>
                          </div>
                          <div class="icheck-info d-inline ml-2">
                            <input type="radio" id="inactive" name="status" value="0" @checked(old('status') == 0 )>
                            <label for="inactive">Inactive</label>
                          </div>
                        </div>
                        @error('status')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Login Method</label>
                        <div class="form-group clearfix">
                          @php $login = !empty(old('login')) ? old('login') : 'regular'; @endphp
                          <div class="icheck-info d-inline mr-2">
                            <input type="radio" id="regular" name="login" value="regular" @checked($login == 'regular' )>
                            <label for="regular">Regular</label>
                          </div>
                          <div class="icheck-info d-inline ml-2">
                            <input type="radio" id="SSO" name="login" value="sso" @checked($login == 'sso' )>
                            <label for="SSO">SSO</label>
                          </div>
                        </div>
                        @error('login')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>
                  @endif
                  <div class="col-md-6">
                    <div class="form-group">
                        <label for="timezone">Time Zone</label>
                        <select name="timezone" id="timezone" class="form-control timezone">
                            @php $tz = old('timezone') ?? 'UTC' @endphp
                            @foreach (timezone_identifiers_list() as $item)
                                <option value="{{$item}}" @selected($item == $tz)>{{$item}}</option>
                            @endforeach
                        </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="url">Website Url (optional)</label>
                      <input type="text" class="form-control @error('url') is-invalid @enderror" id="url" placeholder="Enter website url (optional)" name="url" value="{{ old('url') }}">
                      @error('url')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                </div>
                <button class="btn btn-theme btn-flat" type="submit"><i class="fa-light fa-paper-plane"></i> Submit</button>
              </div>
            </form>
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
    $(function() {
      $('.select2').select2({
        theme: 'bootstrap4',
        sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
        placeholder: "Select Offices",
      })
      $('.select2-customer').select2({
        theme: 'bootstrap4',
        sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
        placeholder: "Select Customer",
      })
      $('.timezone').select2({
        theme: 'bootstrap4',
        placeholder: "Select time zone",
      })

      $('#customer_id').change(function () {
        $('#office_id').html('<option selected disabled></option>');
        getOffice();
      });
      function getOffice(){
        var id = $('#customer_id').val();
        var office = $('#old_office_id').val();
        $.ajax({
          type: "GET",
          url: "{{ route('agent.index') }}"+"/office/"+id+"/"+office,
          success: function (response) {
            if(response.status == 'success'){
              $('#office_id').html(response.offices);
            }
          }
        });
      }
      getOffice();
    });
  </script>
@endpush
