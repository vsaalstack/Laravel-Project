@extends('layouts.admin.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Office Details</h1>
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
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Office Details</div>
            </div>
            <div class="card-body">
              <div class="row">
                <table class="table table-hover">              
                  <tbody>
                    <tr>
                      <td>Name</td>
                      <td>{{ $office->name }}</td>
                    </tr>
                    <tr>
                      <td>Phone</td>
                      <td>{{ $office->user->phone }}</td>
                    </tr>
                    <tr>
                      <td>E-mail</td>
                      <td>{{ $office->user->email }}</td>
                    </tr>
                    <tr>
                      <td>Apify Task Id</td>
                      <td>{{ !empty($office->apify_task_id) ? ucfirst(json_decode($office->apify_task_id)->name) : 'Null' }}</td>                  
                    </tr>
                    <tr>
                      <td>Apify Task Status</td>
                      <td>{{ $office->status == 1 ? 'Enable' : 'Disable' }}</td>
                    </tr>
                    <tr>
                      <td>Office Status</td>
                      <td>{{ $office->user->status == 1 ? 'Active' : 'Inactive' }}</td>
                    </tr>
                    <tr>
                      <td>Login Method</td>
                      <td>{{ ucfirst($office->user->login_method) }}</td>
                    </tr>
                    <tr>
                      <td>Website URL</td>
                      <td>{{ $office->website_url }}</td>
                    </tr>
                    <tr>
                      <td>Listing URL</td>
                      <td>{{ $office->listing_url }}</td>
                    </tr>
                    {{-- <tr>                  
                      <td>Last Crawled</td>                  
                      <td>{{ date('Y-m-d H:i a', strtotime($office->last_crawled. ' '."Asia/Kolkata")) }}</td>                  
                    </tr> --}}
                  </tbody>
                </table>
              </div>
            </div>        
          </div>
        </div>
        @if ($chargebee->count() > 0)
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="card-title">Subscription Details</div>                
              </div> 
              <div class="card-body">
                <table class="table table-hover">
                  <tr>
                    <td>Purchase Date</td>
                    <td>Price</td>
                    <td>Status</td>
                    <td>Next Billing</td>                        
                  </tr>
                  @foreach ($chargebee as $item)
                    <tr>
                      <td>{{ date('F d, Y', $item->purchase_date) }}</td>
                      <td>${{ $item->price/100 }}</td>
                      <td>{{ ucfirst($item->status) }}</td>
                      <td>{{ $item->status == 'active' ? date('F d, Y', $item->next_billing) : '' }}</td>
                    </tr>
                  @endforeach
                </table>
              </div>
            </div>
          </div>            
        @endif
      </div>
      <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
@endsection
