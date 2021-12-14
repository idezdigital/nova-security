@extends('nova::auth.login')

@section('content')

    <form
        class="bg-white shadow rounded-lg max-w-md mx-auto flex overflow-hidden"
        method="POST"
        action="{{ route('nova.login') }}"
    >

        {{ csrf_field() }}

        <div>
            <label class="block font-bold mb-2" for="otp">Código de Segurança</label>
            <input class="form-control form-input form-input-bordered w-full" id="otp" type="text" name="otp"
                   maxlength=6" minlength="6" required autofocus>
            @error('message')
            <p class="text-sm mt-2 text-danger">{{ $message }}</p>
            @enderror
        </div>


        <button class="w-full btn btn-default btn-primary hover:bg-primary-dark mt-6" type="submit">
            {{ __('Login') }}
        </button>

    </form>
@endsection
