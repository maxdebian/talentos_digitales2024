{{-- Modal View --}}
<div class="modal fade" id="showUser-{{$user->id}}"  tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title colorLabelHeaderModal">User</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">



                <div class="card-body">
                    <div class="form-group">
                      <label>Name</label>
                      {{-- <input type="text" class="form-control userDataJSName" disabled> --}}
                      <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" value="{{ $user->email}}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" class="form-control" value="{{ !empty($user->roles[0]) ? $user->roles[0]->name : ''}}" disabled>
                    </div>

                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" value="{{ !empty($user->userdata) ? $user->userdata->first_name : '' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" value="{{ !empty($user->userdata) ? $user->userdata->last_name : '' }}" disabled>
                    </div>
                    <div class="form-group">
                      <label>DNI</label>
                      <input type="text" class="form-control maskDNI" data-inputmask='"mask": "99.999.999"' data-mask  value="{{ !empty($user->userdata) ? $user->userdata->dni : '' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Avatar</label>
                        <img src="{{ !empty($user->userdata) ? url($user->userdata->avatar) : asset('dist/img/user2-160x160.jpg') }}" class="elevation-2 userImage" alt="User Image">
                    </div>
                    <div class="form-group">
                      <label>Address</label>
                      <input type="text" class="form-control" value="{{ !empty($user->userdata) ? $user->userdata->address : '' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Mobile</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            </div>
                            <input type="text" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask  value="{{ !empty($user->userdata) ? $user->userdata->mobile : '' }}" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                      <label>Date of birth</label>
                      <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" placeholder="date_of_birth"  value="{{ !empty($user->userdata) ? $user->userdata->date_of_birth : '' }}" disabled>
                    </div>



                  </div>
                  <!-- /.card-body -->

                {{--   <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
 --}}







            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
  </div>

