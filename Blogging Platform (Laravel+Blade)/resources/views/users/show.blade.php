@extends('layouts.app')

@section('content')
  <div class="row mb-4">
    <div class="col-md-12">
      <x-card class="text-center mb-2" style="margin-top: 80px">
        @if ($user->profileImage)
          <div class="rounded-circle" 
            style="
                  margin: -80px auto;
                  width: 10vw; 
                  height: 10vw; 
                  background-image: url('{{ asset('storage/profiles/' . $user->profileImage->filename) }}');
                  background-size: cover;
                  background-position: center;
                  border: 2px solid #fff;"> 
          </div>
        @else
          <div class="bg-dark rounded-circle" style="
            margin: -80px auto;
            width: 10vw; 
            height:10vw;"></div>
        @endif
        <br/> <br/>
        
        <h2 class="card-title mt-5 mb-0">{{ $user->name }}</h2>
        <small class="card-subtitle mb-2 text-body-secondary">{{ $user->email }}</small>
        <br/> 

        <div class="card-text row mt-3">
          <div class="col-md-4">
            <span class="text-body-secondary d-block">@lang('posts.posts')</span>
            {{ $posts_count }}
          </div>
        </div>

        @profile($user)
          <a href="{{ route('users.edit') }}" class="btn btn-dark btn-sm float-end">
            @lang('users.edit')
          </a>
        @endprofile
      </x-card>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <h2>@lang('posts.last_posts')</h2>

      <div class="space-y-3">
        @each('users/_post', $posts, 'post')
      </div>
    </div>
  </div>
@endsection
