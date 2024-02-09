@extends('layouts.admin.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Modifty Admin</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <a href="{{ route('admin.user') }}" class="btn btn-theme btn-flat"><i class="fa-light fa-arrow-left"></i> Back</a>
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
            <form action="{{ route('user.update',[$user->id]) }}" method="post" enctype="multipart/form-data">@csrf @method('put')
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">User Name</label>
                      <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name" name="name" value="{{ old('name') ?? $user->name }}" required>
                      @error('name')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="timezone">Time Zone</label>
                      <select name="timezone" id="timezone" class="form-control timezone">
                        @php $tz = old('timezone') ?? $user->timezone @endphp
                        @foreach (timezone_identifiers_list() as $item)
                          <option value="{{$item}}" @selected($item == $tz)>{{$item}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Status</label>                     
                      @php $status = old('status') ?? $user->status; @endphp
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
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Login Method</label>
                      <div class="form-group clearfix">
                        @php $login = !empty(old('login')) ? old('login') : $user->login_method	; @endphp
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
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="phone">Phone</label>
                      <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" placeholder="Enter phone" name="phone" value="{{ old('phone') ?? $user->phone }}" required>
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
                        placeholder="Enter Email" name="email" value="{{ old('email') ?? $user->email }}" required>
                      @error('email')
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
    $(function() {
      $('.timezone').select2({
        theme: 'bootstrap4',
        placeholder: "Select time zone",
      })
    });
  </script>
@endpush
