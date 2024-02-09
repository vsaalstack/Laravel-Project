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
          <h1 class="m-0">Billing</h1>
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
        @if (Auth::user()->is_admin == '0')
          <div class="col-12">
            @if (Auth::user()->subscription == '0')
              <div class="card-deck text-center">
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
                        {{-- <p>{{ $plan->item()->description }}</p> --}}
                        <div style="text-align: left">
                          {!! isset($config['subscribe-popup-details']) && !empty($config['subscribe-popup-details']) ? $config['subscribe-popup-details'] : '' !!}
                        </div>
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
            @if ($data['chargebee']->count() > 0)
              <div class="card">
                <div class="card-header">
                  <div class="card-title">Billing History</div>
                  <div class="card-tools">
                    @if (!empty(Auth::user()->chargebee_id) && $data['chargebee']->count() > 0)
                      <button type="button" id="cb-portal" class="btn btn-theme btn-flat"> Manage Subscription </a>
                    @endif
                  </div>
                </div>
                <div class="card-body">
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
                        <td>{{ in_array($item->status, ['active', 'trial']) ? date('F d, Y', $item->next_billing) : '' }}</td>
                      </tr>
                    @endforeach
                  </table>
                </div>
              </div>
            @endif
          </div>
        @endif
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
@endpush
