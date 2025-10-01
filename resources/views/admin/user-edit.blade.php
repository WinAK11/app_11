@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <!-- main-content-wrap -->
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Edit User</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('admin.users') }}">
                            <div class="text-tiny">Users</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit User</div>
                    </li>
                </ul>
            </div>
            <!-- edit-user -->
            <div class="wg-box">
                <form class="form-edit-user form-style-1" action="{{ route('admin.user.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $user->id }}" />
                    <fieldset class="name">
                        <div class="body-title">Name <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Enter Name" name="name" tabindex="0"
                            value="{{ $user->name }}" aria-required="true" required="">
                    </fieldset>
                    @error('name')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror
                    <fieldset class="email">
                        <div class="body-title">Email <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="email" placeholder="Enter Email" name="email" tabindex="0"
                            value="{{ $user->email }}" aria-required="true" required="">
                    </fieldset>
                    @error('email')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror
                    <fieldset class="mobile">
                        <div class="body-title">Mobile <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Enter Mobile Number" name="mobile" tabindex="0"
                            value="{{ $user->mobile }}" aria-required="true" required="">
                    </fieldset>
                    @error('mobile')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror
                    <fieldset class="usertype">
                        <div class="body-title">User Type <span class="tf-color-1">*</span></div>
                        <div class="select flex-grow">
                            <select name="usertype" class="">
                                <option value="USR" {{ $user->usertype == 'USR' ? 'selected' : '' }}>User</option>
                                <option value="ADM" {{ $user->usertype == 'ADM' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('usertype')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror
                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Save</button>
                    </div>
                </form>
            </div>
            <!-- /edit-user -->
        </div>
        <!-- /main-content-wrap -->
    </div>
@endsection