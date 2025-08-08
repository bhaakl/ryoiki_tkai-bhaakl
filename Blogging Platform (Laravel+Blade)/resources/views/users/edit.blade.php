@extends('users.layout', ['tab' => 'profile'])

@section('main_content')
  <x-card>
    <h1>@lang('users.profile')</h1>

    <hr class="my-4">

    <form action="{{ route('users.update') }}" method="POST" enctype="multipart/form-data">
      @method('PATCH')
      @csrf

      <div class="form-group mb-3 row">
        <label for="name" class="form-label col-sm-2 col-form-label">
          @lang('users.attributes.name')
        </label>

        <div class="col-sm-5">
          <input
              type="text"
              id="name"
              name="name"
              @class(['form-control', 'is-invalid' => $errors->has('name')])
              placeholder="@lang('users.placeholder.name')"
              required
              value="{{ old('name', $user->name) }}"
          >

          @error('name')
              <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-group mb-3 row">
        <label for="email" class="form-label col-sm-2 col-form-label">
          @lang('users.attributes.email')
        </label>

        <div class="col-sm-5">
          <input
              type="text"
              id="email"
              name="email"
              @class(['form-control', 'is-invalid' => $errors->has('email')])
              placeholder="@lang('users.placeholder.email')"
              required
              value="{{ old('email', $user->email) }}"
          >

          @error('email')
              <span class="invalid-feedback">{{ $message }}</span>
          @enderror
        </div>
      </div>

      <div class="form-group mb-3">
        <label class="form-label col-sm-2 col-form-label" for="avatar">
            @lang('media.attributes.avatar')
        </label>

        <input
            type="file"
            name="profile_img"
            id="profile_img"
            style="max-width: 300px; min-width: 100px; display: inline-block;"
            accept="image/*"
            @class(['form-control', 'is-invalid' => $errors->has('profile_img')])
          >

          @error('profile_img')
              <span class="invalid-feedback">{{ $message }}</span>
          @enderror
      </div>

      <div class="form-group mb-3 offset-sm-2">
        <button type="submit" class="btn btn-dark">
          <x-icon name="save" />

          @lang('forms.actions.save')
        </button>
      </div>
    </form>
  </x-card>
@endsection