@extends('layouts.admin.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Create Office</h1>
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
            <form action="{{ route('office.store') }}" method="post" enctype="multipart/form-data">@csrf
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Customer</label>
                      <select class="form-control @error('customer_id') is-invalid @enderror select2" style="width: 100%;" id="customer_id" name="customer_id" required>
                        <option value="" selected disabled></option>
                        @foreach ($customers as $customer)
                          <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
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
                      <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Office Name" name="name" value="{{ old('name') }}">
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
                      <label for="apify_task_id">Apify Task Id</label>
                      <input type="hidden" name="" value="{{old('apify_task_id')}}" id="old_apify_task_id">
                      <select class="form-control @error('apify_task_id') is-invalid @enderror select2bs4" style="width: 100%;" name="apify_task_id" id="apify_task_id">

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
                      <label for="website_url">Website Url</label>
                      <input type="text" class="form-control @error('website_url') is-invalid @enderror" id="website_url" 
                        placeholder="Enter Website Url (optional)" name="website_url" value="{{ old('website_url') }}">
                      @error('website_url')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="listing_url">Listing Url</label>
                      <input type="text" class="form-control @error('listing_url') is-invalid @enderror" id="listing_url" 
                        placeholder="Enter Listing Url (optional)" name="listing_url" value="{{ old('listing_url') }}">
                      @error('listing_url')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="status">Apify Task Status</label>
                      <div class="py-2 clearfix"> 
                        <div class="icheck-info d-inline mr-2">
                          <input type="radio" id="on" name="status" value="1" @checked(old('status') == 1)>
                          <label for="on">Enable</label>
                        </div>
                        <div class="icheck-info d-inline ml-2">
                          <input type="radio" id="off" name="status" value="0" @checked(old('status') == 0)>
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
                        <div class="icheck-info d-inline mr-2">
                          <input type="radio" id="officeOn" name="officeStatus" value="1" @checked(old('officeStatus') == 1)>
                          <label for="officeOn">Enable</label>
                        </div>
                        <div class="icheck-info d-inline ml-2">
                          <input type="radio" id="officeOff" name="officeStatus" value="0" @checked(old('officeStatus') == 0)>
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
                        @php $login = old('login') ?? 'regular' @endphp
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
    $('#customer_id').change(function () { 
      $('#apify_task_id').html('<option selected disabled></option>');
      getTask();
    });
    $('.select2').select2({
      sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
      placeholder: "Select Customer",
      theme: 'bootstrap4',
    })
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
