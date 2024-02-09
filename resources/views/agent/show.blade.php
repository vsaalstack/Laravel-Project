@extends('layouts.admin.app')

@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Agent Details</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <a href="{{ url()->previous() }}" class="btn btn-info"><i class="fa-solid fa-arrow-left"></i> Back</a>
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
      <div class="card card-outline card-primary">
        <div class="card-header">
          <h3 class="card-title">{{ $agent->user->name }}</h3>
          <div class="card-tools">
            {{-- <a href="#"><i class="fa-solid fa-share-nodes"></i> <b>Share</b></a> --}}
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <table class="table table-bordered">
              {{-- <tr>
                <td>Name</td>
                <td>{{ $customer->name }}</td>
              </tr>
              <tr>
                <td>URL</td>
                <td>{{ $customer->url }}</td>
              </tr>
              <tr>
                <td>Apify Actor Id</td>
                <td>{{ $customer->apify_actor_id }}</td>
              </tr> --}}
            </table>
          </div>
        </div>
        <div class="card-footer">
          <a href="{{ route('agent.edit', [$agent->id]) }}" class="btn btn-warning"><i class="fa-solid fa-edit"></i>
            Edit</a>
        </div>
      </div>
      <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
@endsection
