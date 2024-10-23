@extends('layouts.app')
@section('content')



    <div class="row rowFixed">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title titleModule">User List</h3> <a href="{{ route('user.create') }}" class="btn float-right colorCyan" role="button">+ Add User</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="listUserTD" class="table table-bordered table-striped">

                        <thead>
                            <tr>
                                <th style="width:15%; text-align:center">Avatar</th>
                                <th style="width:15%; text-align:center">Name</th>
                                <th style="width:15%; text-align:center">UserName</th>
                                <th style="width:10%; text-align:center">Type</th>
                                <th style="width:15%; text-align:center">Email</th>
                                <th style="width:20%; text-align:center">First Name, Last Name</th>
                                <th style="text-align:center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr id='userId_{{$user->id}}'>
                                    <td style="text-align: center">
                                        @if(!empty($user->userdata))
                                            <img src="{{ url($user->userdata->avatar) }}" class="elevation-2 userImage" alt="User Image">
                                        @else
                                            <img src="{{ asset('/dist/img/user2-160x160.jpg') }}" class="elevation-2 userImage" alt="User Image">
                                        @endif
                                    </td>
                                    <td>
                                        @if(($user) && ($user->name))
                                            <span class="textFirstName"> {{ $user->name }}</span>
                                        @endif
                                    </td>
                                    <td style=" text-align:center">
                                        @if(!empty($user->username))
                                            <span class="textFirstName">{{ $user->username }}</span>
                                        @endif
                                    </td>
                                    <td style=" text-align:center">
                                        @if(!empty($user->roles))
                                            @foreach ($user->roles as $itemRole)
                                                <span class="textFirstName">{{ $itemRole->name }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style=" text-align:center">
                                        @if(!empty($user->email))
                                            <span class="textFirstName">{{ $user->email }}</span>
                                        @endif
                                    </td>
                                    <td style=" text-align:center">
                                        @if(!empty($user->userdata))
                                        <span class="textFirstName">{{ $user->userdata->first_name }}</span>, <span class="textLastName">{{ $user->userdata->last_name }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn paddBto" data-user="{{$user}}" data-toggle="modal" data-target="#showUser-{{$user->id}}" data-title="View">
                                                <svg width="1.2em" height="1.2em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="#4099D4" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                                </svg>
                                            </button>
                                            {{-- <button type="button" onclick="showUser({{$user->id}})"></button> --}}
                                            <form action="{{$user->id}}/edit" method="GET">
                                                <button type="submit" class="btn" data-title="Edit">
                                                    <i class="far fa-edit"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn paddBto" onclick="deleteUser({{$user->id}})" data-title="Delete">
                                                <i class="fas fa-trash-alt" style="color:red"></i>
                                            </button>

                                        </div>
                                    </td>
                                </tr>
                                @include('user/partials/actions')
                            @endforeach
                        </tbody>
                        <tfoot>
                            <thead>
                                <tr>
                                    <th style="width:15%; text-align:center">Avatar</th>
                                    <th style="width:15%; text-align:center">Name</th>
                                    <th style="width:15%; text-align:center">UserName</th>
                                    <th style="width:10%; text-align:center">Type</th>
                                    <th style="width:15%; text-align:center">Email</th>
                                    <th style="width:20%; text-align:center">First Name, Last Name</th>
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

    <script src="{{ asset('js/modules/user/list.js') }}"></script>

@endsection
