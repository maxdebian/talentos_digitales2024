@extends('layouts.app')

@section('content')



    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Create Product</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- form start -->
                <form method="post" action="{{ route('product.store') }}" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        @include('product.partials.form')
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

@endsection

@section('scripts')
    <script src="{{ asset('js/modules/product/forms.js') }}"></script>
@endsection
