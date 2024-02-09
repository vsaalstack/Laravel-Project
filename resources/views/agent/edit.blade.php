@extends('layouts.admin.app')

@push('css')
  <script src="https://js.chargebee.com/v2/chargebee.js"></script>
@endpush

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Edit Agent Details</h1>
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
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="card-title">
                <a href="{{ route('agent.edit',[$agent->id]).'?tab=details' }}" class="btn btn-flat @if($data['tab'] == 'details') btn-theme @endif">Agent Details</a>
                <a href="{{ route('agent.edit',[$agent->id]).'?tab=settings' }}" class="btn btn-flat @if($data['tab'] == 'settings') btn-theme @endif">Agent Settings</a>
                <a href="{{ route('agent.edit',[$agent->id]).'?tab=subscriptions' }}" class="btn btn-flat @if($data['tab'] == 'subscriptions') btn-theme @endif">Agent Subscriptions</a>
              </div>
            </div>
            <form action="{{ route('agent.update', [$agent->id]) }}" method="post" enctype="multipart/form-data">@csrf
              @method('put')
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Customer</label>
                      <select class="form-control @error('customer_id') is-invalid @enderror select2-customer" style="width: 100%;" name="customer_id" id="customer_id" required placeholder="Select Customer">
                        @php $customer_id = old('customer_id') ?? $agent->customer_id @endphp 
                        <option value="" selected disabled></option>
                        @foreach ($customers as $customer)
                          <option value="{{ $customer->id }}" @selected($customer->id == $customer_id)>{{ $customer->name }}</option>
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
                      @php $office_id = empty(old('office_id')) ? $agent->office_id : implode(',',old('office_id'))  @endphp
                      <input type="hidden" value="{{ $office_id }}" id="old_office_id">                      
                      <select class="form-control @error('office_id') is-invalid @enderror select2" style="width: 100%;" name="office_id[]" id="office_id" required multiple placeholder="Select offices">                        
                        {{-- @foreach ($offices as $office)
                          <option value="{{ $office->id }}" @selected(in_array($office->id, $office_id))>{{ $office->customer->name.' - '.$office->name }}</option>
                        @endforeach --}}
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
                      <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" value="{{ old('name') ?? $agent->user->name }}" @if (Auth::user()->is_admin == '0') readonly @endif>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="phone">Phone</label>
                      <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                        placeholder="Enter phone" name="phone" value="{{ old('phone') ?? $agent->phone }}">
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
                      <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        placeholder="Enter Email" name="email" value="{{ old('email') ?? $agent->user->email}}">                        
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
                      <input type="text" class="form-control" id="instagram_handle" placeholder="Instagram Handle" name="instagram_handle" value="{{ old('instagram_handle') ?? $agent->instagram_handle }}">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Status</label>
                      @php $status = old('status') ?? $agent->user->status @endphp
                      <div class="form-group clearfix">
                        <div class="icheck-info d-inline mr-2">
                          <input type="radio" id="active" name="status" value="1" @checked($status == 1 )>
                          <label for="active">Active</label>
                        </div>
                        <div class="icheck-info d-inline ml-2">
                          <input type="radio" id="inactive" name="status" value="0" @checked($status == 0 )>
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
                  @if (in_array(Auth::user()->role,['admin','super-admin']))
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Login Method</label>
                        <div class="form-group clearfix">
                          @php $login = old('login') ?? $agent->user->login_method @endphp
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
                            @php $tz = old('timezone') ?? $agent->user->timezone @endphp
                            @foreach (timezone_identifiers_list() as $item)
                                <option value="{{$item}}" @selected($item == $tz)>{{$item}}</option>
                            @endforeach
                        </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="url">Website Url (optional)</label>
                      <input type="text" class="form-control @error('url') is-invalid @enderror" id="url"
                        placeholder="Enter website url (optional)" name="url"
                        value="{{ old('url') ?? $agent->url }}">
                      @error('url')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                </div>
                <button class="btn btn-theme btn-flat" type="submit"><i class="fa-light fa-paper-plane"></i> Update</button>
                <a href="{{ route('agent.password',[$agent->id]) }}" class="btn btn-theme btn-flat"><i class="fa-light fa-user-lock"></i> Password Reset</a>
              </div>              
            </form>
          </div>
          <!-- /.card -->
        </div>       
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
