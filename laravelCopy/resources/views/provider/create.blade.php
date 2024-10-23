@extends('layouts.app')

@extends('layouts.menu')
@section('content')
<!-- Content Wrapper. Contains page content -->
{{-- <div class="content-wrapper"> --}}



    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Create Provider</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- form start -->
                <form method="post" action="{{ route('provider.store') }}" autocomplete="off">
                        @csrf
                        @include('provider.partials.form')
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                </form>
              </div>


            </div>
            <!-- /.card -->



          </div>
          <!--/.col (left) -->

        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

{{-- </div> --}}
@endsection

@section('scripts')
    <script src="{{ asset('js/modules/provider/forms.js') }}"></script>
@endsection
