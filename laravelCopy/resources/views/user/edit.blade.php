@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Edit User</h3>
              </div>
              <!-- /.card-header -->

              <!-- form start -->
              <form method="POST" action="{{ route('user.update',$user->id) }}" onsubmit="return validateForm();" enctype="multipart/form-data" autocomplete="off">
                    @method('PUT')
                    @csrf
                    @include('user.partials.form')
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                      </div>

              </form>
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
    <script src="{{ asset('js/modules/user/form.js') }}"></script>
@endsection
