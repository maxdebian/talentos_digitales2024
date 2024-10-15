@extends('layouts.app')

@section('content')

            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Crear Obra Social</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ route('obras_sociales.storeObraSocial') }}" method="post">
                    @csrf
                    <div class="card-body">
                      @include('obra_social.partials.forms')

                  </div>
                  <!-- /.card-body -->

                  <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
              </div>
              <!-- /.card -->
@endsection
