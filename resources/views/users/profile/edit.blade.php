@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div class="row justify-content-center">
        <div class="col-8">
            <form action="{{route('profile.update')}}" method="post" class="bg-white shadow rounded-3 p-5" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <h2 class="h3 mb-3 fw-light text-muted">Update Profile</h2>
                <div class="row mb-3">
                    <div class="col-4">
                        @if ($user->avatar)
                            <img src="{{asset('storage/avatars/' . $user->avatar)}}" alt="{{$user->avatar}}" class="img-thumbnail rounded-cricle d-block mx-auto profile-avatar">
                        @else
                            <i class="fa-solid fa-circle-user text-secondary d-block text-center profile-icon"></i>
                        @endif
                    </div>
                    <div class="col align-self-end">
                        <input type="file" name="avatar" id="avatar" class="form-control form-control-sm mt-1" aria-describedby="avatar-info">
                        <div class="form-text" id="avatar-info">
                            Acceptable formats: jpeg,jpg,png and gif only. <br>
                            Maximum file size is 1048Kb
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label fw-">Nam</label>bold
                    <input type="text" name="name" id="name" class="form-control" value="{{old('name', $user->name)}}" autofocus>

                    @error('name')
                        <p class="text-danger small">{{$message}}</p>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{old('email', $user->email)}}">
                    @error('email')
                        <p class="text-danger small">{{$message}}</p>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="introduction" class="form-label fw-bold">Introduction</label>
                    <textarea name="introduction" id="introduction" rows="5" class="form-control" placeholder="Describe Yourself">{{old('introduction', $user->introduction)}}</textarea>

                    @error('introduction')
                        <p class="text-danger small">{{$message}}</p>
                    @enderror
                </div>
                <button type="submit" class="btn btn-warning px-5">Save</button>
            </form>
        </div>
    </div>
@endsection
