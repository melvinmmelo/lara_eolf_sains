@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">

            <div class="card-body">

                @include('layouts.errors')

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Contact No.</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>

                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->contact_no }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach ($user->roles as $role)
                                        {{ $role->name }}
                                    @endforeach

                                </td>
                                <td>{!! statusEmployeeBadge($user->status) !!}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                        <button type="button" class="btn btn-success" data-toggle="modal"
                                            data-target="#editUser"
                                            onclick="setToUpdateUser('{{ $user->id }}','{{ $user->last_name }}','{{ $user->first_name }}','{{ $user->contact_no }}','{{ $user->address }}', '{{ $user->roles->first() ? $user->roles->first()->name : '' }}','{{ $user->status }}')"> <i class="fas fa-edit"></i>
                                        </button>
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button" class="btn btn-danger dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                <a class="dropdown-item"
                                                    href="{{ route('user.delete', ['id' => $user->id]) }}"
                                                    onclick="return deleteUser();">
                                                    Delete
                                                </a>
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target='#resetUser' onclick="setUserToReset('{{ $user->id }}','{{ $user->last_name }}','{{ $user->first_name }}')">
                                                    Reset password
                                                </a>
                                            </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-users">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->


        <div class="modal fade" id="modal-users">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Users</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="name">Last Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="last_name">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="name">First Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="email">E-mail<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="email">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="password" value="Eolf@2024">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="password">Confirm Password<span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control"
                                            value="Eolf@2024">
                                    </div>
                                </div>
                            </div>

                            <p class="text-smaller text-muted">The default password is Eolf@2024</p>

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="role">Role</label>
                                        <select name="role" id="role" class="form-control">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="active">Active</option>
                                            <option value="resigned">Resigned</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- /.modal-content -->
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </form>
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->


    </section>


    <!-- /.content -->
    </form>
    </div>
    </div>
    {{-- End Adding user modal --}}


    {{-- Editing user modal --}}
    <div class="modal fade" id="editUser">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('user.update') }}">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit User</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <input type="hide" class="form-control" name="user_id" id="user_id" required readonly
                            hidden>

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="e_lname">Last Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="e_lname" id="e_lname">
                                </div>


                                <div class="col-sm-6">
                                    <label class="form-label" for="e_fname">First Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="e_fname" id="e_fname">
                                </div>

                            </div>
                        </div>


                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <label class="form-label" for="e_cno">Contact No<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="e_cno" id="e_cno">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <label class="form-label" for="e_addr">Address</label>
                                    <input type="text" class="form-control" name="e_address" id="e_addr">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <label class="form-label" for="e_role">Role<span class="text-danger">*</span></label>
                                    <select name="e_role" id="e_role" class="form-control">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <label class="form-label" for="e_status">Status<span class="text-danger">*</span></label>
                                    <select name="e_status" id="e_status" class="form-control">
                                        <option value="active">Active</option>
                                        <option value="resigned">Resigned</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
                <!-- /.modal -->
            </form>
        </div>
    </div>
    {{-- End Editing user modal --}}

    {{-- Reset password user modal --}}
    <div class="modal fade" id="resetUser">
        <div class="modal-dialog">
            <form id="resetForm" method="POST" action="{{ route('user.reset') }}">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="resetHeaderTitle">Reset User Password</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" class="form-control" name="ruser_id" id="ruser_id" required readonly>

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <label class="form-label" for="password">New Password<span class="text-danger">*</span></label>
                                    <input type="text" name="password" class="form-control"
                                        value="Eolf@2024">
                                </div>
                            </div>
                        </div>


                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
                <!-- /.modal -->
            </form>
        </div>
    </div>
    {{-- End Reset password user modal --}}
@endsection


@section('custom_js')
    <script>
        function setToUpdateUser(uid, ln, fn, con, addr, role, status) {
            document.getElementById("user_id").value = uid;
            document.getElementById("e_lname").value = ln;
            document.getElementById("e_fname").value = fn;
            document.getElementById("e_cno").value = con;
            document.getElementById("e_addr").value = addr;
            document.getElementById("e_role").value = role;
            document.getElementById("e_status").value = status;
        }

        function setUserToReset(uid, ln, fn) {
            document.getElementById("resetHeaderTitle").textContent = `${ln} ${fn} - Reset Password`;

            // document.getElementById("resetForm").action = "/usereset/" + uid;
            document.getElementById("ruser_id").value = uid;
        }

        function deleteUser() {
            return confirm('Are you sure you want to delete this user?');
        }
    </script>
@endsection
