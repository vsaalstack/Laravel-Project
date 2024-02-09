@extends('layouts.admin.app')

@push('css')
  <script src="https://js.chargebee.com/v2/chargebee.js"></script>
@endpush

{{-- @dd(empty(Auth::user()->agent()->name)) --}}
@section('content')
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Profile</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active">Profile</li>
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
        <div class="col-md-3">
          <div class="card">
            <div class="card-body box-profile">
              <div class="text-center">
                @if (Auth::user()->role == 'agent' && !empty(Auth::user()->profile_photo))
                  <img class="profile-user-img img-fluid img-circle" src="{{ $imgCDN . Auth::user()->profile_photo . '?w=80&h=80&func=face&face_margin=40' }}" alt="Agent profile picture">
                @else
                  <img class="profile-user-img img-fluid img-circle" src="{{ asset('dist/img/user.png') }}" alt="User profile picture">
                @endif
              </div>
              <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>
              <p class="text-muted text-center">Member since {{ date('M Y', strtotime(Auth::user()->created_at)) }}</p>
            </div>
          </div>
        </div>
        <div class="col-md-9">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Profile Details</div>
            </div>
            <!-- /.card-header -->
            <form action="{{ route('user.update', [Auth::user()->id]) }}" method="post" enctype="multipart/form-data">@csrf @method('put')
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Name</label>
                      <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter name" name="name" value="{{ old('name') ?? Auth::user()->name }}"
                        @if (Auth::user()->is_admin == '0') readonly @endif>
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
                      <select name="timezone" id="timezone" class="form-control select2bs4">
                        @php $tz = old('timezone') ?? Auth::user()->timezone @endphp
                        @foreach (timezone_identifiers_list() as $item)
                          <option value="{{ $item }}" @selected($item == $tz)>{{ $item }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="phone">Phone</label>
                      <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" placeholder="Enter phone" name="phone" value="{{ old('phone') ?? Auth::user()->phone }}" required>
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
                      <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter email" name="email" value="{{ old('email') ?? Auth::user()->email }}">
                      @error('email')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  @if (Auth::user()->role == 'agent')
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="instagram_handle">Instagram Handle</label>
                        <input type="text" class="form-control @error('instagram_handle') is-invalid @enderror" id="instagram_handle" placeholder="Enter Instagram Handle" name="instagram_handle"
                          value="{{ old('instagram_handle') ?? !empty(Auth::user()->agent->instagram_handle) ? Auth::user()->agent->instagram_handle : '' }}">
                        @error('instagram_handle')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>
                  @endif
                </div>
                <button class="btn btn-theme btn-flat" type="submit"><i class="fa-light fa-paper-plane"></i> Update</button>
              </div>
            </form>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        {{-- @if (Auth::user()->is_admin == '0')
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="card-title">Subscription Details</div>
                <div class="card-tools">
                  @if (!empty(Auth::user()->chargebee_id) && $data['chargebee']->count() > 0)
                    <button type="button" id="cb-portal" class="btn btn-theme btn-flat"> Manage Subscription </a>
                  @endif
                </div>
              </div>
              <div class="card-body">
                @if ($data['chargebee']->count() > 0)
                  <div class="card p-2">
                    <table class="table table-hover">
                      <tr>
                        <td>Purchase Date</td>
                        <td>Price</td>
                        <td>Status</td>
                        <td>Next Billing</td>
                      </tr>
                      @foreach ($data['chargebee'] as $item)
                        <tr>
                          <td>{{ date('F d, Y', $item->purchase_date) }}</td>
                          <td>${{ $item->price / 100 }}</td>
                          <td>{{ ucfirst($item->status) }}</td>
                          <td>{{ in_array($item->status,['active','trial']) ? date('F d, Y', $item->next_billing) : '' }}</td>
                        </tr>
                      @endforeach
                    </table>
                  </div>
                @endif
                @if (Auth::user()->subscription == '0')
                  <div class="card-deck mb-3 text-center">
                    @foreach ($data['plans'] as $plan)
                      @if (ucfirst(Auth::user()->role) == $plan->item()->id && $plan->item()->status == 'active' && str_contains($data['customer'], 'Harcourts') == false)
                        <div class="card mb-3 box-shadow">
                          <div class="overlay dark fb-overlay" style="display: none;">
                            <h5 class="text-light"><i class="fas fa-sync fa-spin mr-2"></i>Please wait....</h5>
                          </div>
                          <div class="card-header">
                            <h4 class="my-0 font-weight-normal">{{ $plan->item()->name }}</h4>
                          </div>
                          <div class="card-body">
                            <p>{{ $plan->item()->description }}</p>
                            @foreach ($data['planPrice'] as $price)
                              @if ($price->itemPrice()->itemId == $plan->item()->id && $price->itemPrice()->status == 'active')
                                <button type="button" data-plan="{{ $price->itemPrice()->id }}" class="btn btn-flat btn-theme cb-checkout">
                                  ${{ $price->itemPrice()->price / 100 . ' ' . $price->itemPrice()->currencyCode . ' / ' . ucfirst($price->itemPrice()->periodUnit) }}
                                </button>
                              @endif
                            @endforeach
                            <p class="mt-1 mb-0 text-sm"> Note : +gst if applicable</p>
                          </div>
                        </div>
                      @elseif(Auth::user()->role == 'agent' && $plan->item()->id == 'Harcourts-Agent-Subscription' && $plan->item()->status == 'active' && str_contains($data['customer'], 'Harcourts'))
                        <div class="card mb-3 box-shadow">
                          <div class="overlay dark fb-overlay" style="display: none;">
                            <h5 class="text-light"><i class="fas fa-sync fa-spin mr-2"></i>Please wait....</h5>
                          </div>
                          <div class="card-header">
                            <h4 class="my-0 font-weight-normal">{{ $plan->item()->name }}</h4>
                          </div>
                          <div class="card-body">
                            <p>{{ $plan->item()->description }}</p>
                            @foreach ($data['planPrice'] as $price)
                              @if ($price->itemPrice()->itemId == $plan->item()->id && $price->itemPrice()->status == 'active')
                                <button type="button" data-plan="{{ $price->itemPrice()->id }}" class="btn btn-flat btn-theme cb-checkout">
                                  ${{ $price->itemPrice()->price / 100 . ' ' . $price->itemPrice()->currencyCode . ' / ' . ucfirst($price->itemPrice()->periodUnit) }}
                                </button>
                              @endif
                            @endforeach
                            <p class="mt-1 mb-0 text-sm"> Note : +gst if applicable</p>
                          </div>
                        </div>
                      @elseif(Auth::user()->role == 'office' && $plan->item()->id == 'Harcourts-Office-Subscription' && $plan->item()->status == 'active' && str_contains($data['customer'], 'Harcourts'))
                        <div class="card mb-3 box-shadow">
                          <div class="overlay dark fb-overlay" style="display: none;">
                            <h5 class="text-light"><i class="fas fa-sync fa-spin mr-2"></i>Please wait....</h5>
                          </div>
                          <div class="card-header">
                            <h4 class="my-0 font-weight-normal">{{ $plan->item()->name }}</h4>
                          </div>
                          <div class="card-body">
                            <p>{{ $plan->item()->description }}</p>
                            @foreach ($data['planPrice'] as $price)
                              @if ($price->itemPrice()->itemId == $plan->item()->id && $price->itemPrice()->status == 'active')
                                <button type="button" data-plan="{{ $price->itemPrice()->id }}" class="btn btn-flat btn-theme cb-checkout">
                                  ${{ $price->itemPrice()->price / 100 . ' ' . $price->itemPrice()->currencyCode . ' / ' . ucfirst($price->itemPrice()->periodUnit) }}
                                </button>
                              @endif
                            @endforeach
                            <p class="mt-1 mb-0 text-sm"> Note : +gst if applicable</p>
                          </div>
                        </div>
                      @endif
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          </div>
        @endif --}}
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
@endsection

{{-- @push('script')
  <script>
    $(document).ready(function() {
      var cbInstance = window.Chargebee.init({
        site: "{{ $config['chargebee_site'] }}"
      });

      cbInstance.setPortalSession(() => {
        return $.ajax({
          method: "post",
          data: {
            _token: "{{ csrf_token() }}",
          },
          url: "{{ route('chargebee.session') }}",
        });
      });

      $(".cb-checkout").on("click", function(event) {
        $('.fb-overlay').show();
        var planId = $(this).data('plan');
        var chargebee = "{{ Auth::user()->chargebee_id }}"

        function chekout() {
          event.preventDefault();
          event.stopPropagation();
          cbInstance.openCheckout({
            hostedPage: function() {
              return $.ajax({
                method: "post",
                url: "{{ route('chargebee.index') }}",
                data: {
                  plan: planId,
                  _token: "{{ csrf_token() }}",
                }
              });
            },
            close: function() {
              setTimeout(function() {
                location.reload()
              }, 5000);
            },
          });
        }
        if (chargebee == '') {
          $.ajax({
            type: "post",
            url: "{{ route('chargebee.create') }}",
            data: {
              user: "{{ Auth::id() }}",
              _token: "{{ csrf_token() }}",
            },
            success: function(response) {
              chekout()
            }
          });
        } else {
          chekout()
        }
      });

      $("#cb-portal").on("click", function(event) {
        event.stopPropagation();
        event.preventDefault();
        cbInstance.createChargebeePortal().open({
          loaded: function() {},
          close: function() {
            setTimeout(function() {
              location.reload()
            }, 5000);
          },
        })
      });
    });
  </script>
@endpush --}}
