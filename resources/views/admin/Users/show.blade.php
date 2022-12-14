@extends('admin.admin_template')

{{-- Administrative Panel Required Fields  --}}
@php
    $title = 'Users';
    $breadcrumbs = ['Home'=> route('admin.home'),'Users'=> route('users.index'), 'Show'=>false];
@endphp

@section('content')
    <div class="card mt-3 mt-md-0">
        <div class="card-body">
            <div class="nav nav-tabs nav-bordered mb-3">
                <div class="container-fluid">
                    <div class="float-start">
                        <h2>Show User <small><a href="{{route('users.index')}}" class=""> <i class="dripicons-arrow-thin-left "></i>Back to the List</a> </small></h2>
                    </div>
                </div>

            </div>

            <div class="tab-pane show active">

                    <div class="row">
                        <div class="col-lg-6 mb-3 col-md-12">
                            <label for="simpleinput" class="form-label">Name</label>
                            <input type="text" id="simpleinput" class="form-control" value="{{ $user->name }}" disabled="disabled">
                        </div>

                        <div class="col-lg-6 mb-3 col-md-12">
                            <label for="simpleinput" class="form-label">Email</label>
                            <input type="text" id="simpleinput" class="form-control" value="{{ $user->email }}" disabled="disabled">
                        </div>
                    </div>

                    <div class="row">
                        <h4>Status</h4>
                        <div class="col-12 mb-3">
                            @if($user->active)
                                <h3>
                                    <span class="badge bg-primary rounded-pill">Active</span>
                                </h3>
                            @else
                                <h3>
                                    <span class="badge bg-danger  rounded-pill">Disabled</span>
                                </h3>
                            @endif
                        </div>
                    </div>

                    @php($user_roles = $user->getRoleNames())
                    @if($user_roles != null)
                        <div class="row">
                            <h4>Permission</h4>
                            <div class="col-12 mb-3">
                                @foreach($roles as $role)
                                    @if($user_roles[0] == $role->name)
                                        <div class="form-check form-check-inline">
                                            <input type="radio" id="role" name="role" value="{{$role->name}}" class="form-check-input" checked disabled>
                                            <label class="form-check-label" for="role">{{$role->name}}</label>
                                        </div>
                                    @else
                                        <div class="form-check form-check-inline">
                                            <input type="radio" id="role" name="role" value="{{$role->name}}" class="form-check-input"  disabled>
                                            <label class="form-check-label" for="role">{{$role->name}}</label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-6">
                            <a class="text-white" href="{{ route('users.edit',$user->id) }} ">
                                <button class="btn btn-success " type="button"> Edit </button>
                            </a>
                            <button  type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#danger-header-modal">Delete</button>
                        </div>
                    </div>

            </div>
        </div>
    </div>
    @include('admin.Crud.delete_modal')
@endsection
