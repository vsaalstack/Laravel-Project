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
          <h1 class="m-0">Update Office Details</h1>
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
                <a href="{{ route('office.edit',[$office->id]).'?tab=details' }}" class="btn btn-flat @if($data['tab'] == 'details') btn-theme @endif">Office Details</a>
                <a href="{{ route('office.edit',[$office->id]).'?tab=settings' }}" class="btn btn-flat @if($data['tab'] == 'settings') btn-theme @endif">Office Settings</a>
                <a href="{{ route('office.edit',[$office->id]).'?tab=subscriptions' }}" class="btn btn-flat @if($data['tab'] == 'subscriptions') btn-theme @endif">Office Subscriptions</a>
              </div>
            </div>
            <form action="{{ route('office.update', [$office->id]) }}" method="post" enctype="multipart/form-data">
              @csrf @method('put')
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Customer</label>
                      @php $cust_id = old('customer_id') ?? $office->customer_id @endphp
                      <select class="form-control @error('customer_id') is-invalid @enderror select2" style="width: 100%;" name="customer_id" id="customer_id" required>
                        <option value="" selected disabled></option>
                        @foreach ($customers as $customer)
                          <option value="{{ $customer->id }}" @selected($cust_id == $customer->id)>{{ $customer->name }}</option>
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
                      <label for="name">Office Name</label>
                      <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Office Name"
                        name="name" value="{{ old('name') ?? $office->name }}" @if (Auth::user()->is_admin == '0') readonly @endif>
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
                      <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" placeholder="Enter phone"
                        name="phone" value="{{ old('phone') ?? $user->phone }}">
                      @error('phone')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="apify_task_id">Apify Task Id</label>
                      @php
                        if (old('apify_task_id') != null) {
                          $actor_id = old('apify_task_id');
                        } else {
                          $actor_id = !empty($office->apify_task_id) ? json_decode($office->apify_task_id)->id."#trev@".json_decode($office->apify_task_id)->name : NUll;
                        }
                      @endphp
                      <input type="hidden" name="" value="{{ $actor_id }}" id="old_apify_task_id">
                      <select class="form-control @error('apify_task_id') is-invalid @enderror select2bs4" style="width: 100%;" id="apify_task_id" name="apify_task_id">

                      </select>
                      @error('apify_task_id')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="email">Email</label>
                      <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter Email" name="email" value="{{ old('email') ?? $user->email }}">
                      @error('email')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="timezone">Time Zone</label>
                      <select name="timezone" id="timezone" class="form-control select2bs4">
                        @php $tz = old('timezone') ?? $user->timezone @endphp
                        @foreach (timezone_identifiers_list() as $item)
                          <option value="{{$item}}" @selected($item == $tz)>{{$item}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="website_url">Website Url</label>
                      <input type="text" class="form-control @error('website_url') is-invalid @enderror" id="website_url" placeholder="Enter Website Url (optional)" name="website_url" value="{{ old('website_url') ?? $office->website_url }}">
                      @error('website_url')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="listing_url">Listing Url</label>
                      <input type="text" class="form-control @error('listing_url') is-invalid @enderror" id="listing_url" placeholder="Enter Listing Url (optional)" name="listing_url" value="{{ old('listing_url') ?? $office->listing_url }}">
                      @error('listing_url')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Apify Task Status</label>
                      <div class="py-2 clearfix">
                        @php $status = old('status') ?? $office->status @endphp
                        <div class="icheck-info d-inline mr-2">
                          <input type="radio" id="on" name="status" value="1" @checked($status == 1)>
                          <label for="on">Enable</label>
                        </div>
                        <div class="icheck-info d-inline ml-2">
                          <input type="radio" id="off" name="status" value="0" @checked($status == 0)>
                          <label for="off">Disable</label>
                        </div>
                      </div>
                      @error('status')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="officeStatus">Office Status</label>
                      <div class="py-2 clearfix">
                        @php $officeStatus = old('officeStatus') ?? $user->status @endphp
                        <div class="icheck-info d-inline mr-2">
                          <input type="radio" id="officeOn" name="officeStatus" value="1" @checked($officeStatus == 1)>
                          <label for="officeOn">Enable</label>
                        </div>
                        <div class="icheck-info d-inline ml-2">
                          <input type="radio" id="officeOff" name="officeStatus" value="0" @checked($officeStatus == 0)>
                          <label for="officeOff">Disable</label>
                        </div>
                      </div>
                      @error('officeStatus')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Login Method</label>
                      <div class="form-group clearfix">
                        @php $login = old('login') ?? $user->login_method @endphp
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
                </div>
                <button class="btn btn-theme btn-flat" type="submit"><i class="fa-light fa-paper-plane"></i> Update</button>
                <a href="{{ route('user.password',[$user->id]) }}" class="btn btn-theme btn-flat"><i class="fa-light fa-user-lock"></i> Password Reset</a>
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
    $('.select2').select2({
      sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
      placeholder: "Select Customer",
      theme: 'bootstrap4',
    })
    $('#customer_id').change(function () {
      $('#apify_task_id').html('<option selected disabled></option>');
      getTask();
    });
    function getTask(){
      var id = $('#customer_id').val();
      $.ajax({
        type: "GET",
        url: "{{ route('office.index') }}"+"/apifyTask/"+id,
        success: function (response) {
          if(response.status == 'success'){
            $('#apify_task_id').html(response.taskList);
            if ($('#old_apify_task_id').val()){
              $('#apify_task_id').val($('#old_apify_task_id').val());
            }
          }
        }
      });
    }
    getTask();
  </script>
@endpush
