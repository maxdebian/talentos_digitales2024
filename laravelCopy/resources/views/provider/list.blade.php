@extends('layouts.app')

@extends('layouts.menu')
@section('content')



    <div class="row rowFixed">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title titleModule">Provider List</h3> <a href="{{ route('provider.create') }}" class="btn float-right colorCyan" role="button">+ Add Provider</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="listProvider" class="table table-bordered table-striped">

                        <thead>
                            <tr>
                                <th style="width:20%; text-align:center">First Name</th>
                                <th style="width:20%; text-align:center">Last Name</th>
                                <th style="width:20%; text-align:center">Address</th>
                                <th style="width:20%; text-align:center">Mobile</th>
                                <th style="text-align:center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($providers as $provider)
                                <tr id='providerId_{{$provider->id}}'>
                                    <td>
                                        <span class="textFirstName"> {{ $provider->first_name }}</span>
                                    </td>
                                    <td style=" text-align:center">
                                        <span class="textFirstName">{{ $provider->last_name }}</span>
                                    </td>
                                    <td style=" text-align:center">
                                        <span class="textFirstName">{{ $provider->address }}</span>
                                    </td>
                                    <td style=" text-align:center">
                                        <span class="textFirstName">{{ $provider->mobile }}</span></span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn paddBto" data-user="{{$provider}}" data-toggle="modal" data-target="#showUser-{{$provider->id}}" data-title="View">
                                                <svg width="1.2em" height="1.2em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="#4099D4" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                                </svg>
                                            </button>
                                            <form action="{{$provider->id}}/edit" method="GET">
                                                <button type="submit" class="btn" data-title="Edit">
                                                    <i class="far fa-edit"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn paddBto" onclick="deleteUser({{$provider->id}})" data-title="Delete">
                                                <i class="fas fa-trash-alt" style="color:red"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                                @include('provider/partials/actions')
                            @endforeach
                        </tbody>
                        <tfoot>
                            <thead>
                                <tr>
                                    <th style="width:20%; text-align:center">First Name</th>
                                    <th style="width:20%; text-align:center">Last Name</th>
                                    <th style="width:20%; text-align:center">Address</th>
                                    <th style="width:20%; text-align:center">Mobile</th>
                                    <th style="text-align:center">Actions</th>
                                </tr>
                            </thead>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>





@endsection

@section('scripts')

    <script src="{{ asset('js/modules/provider/list.js') }}"></script>

@endsection
