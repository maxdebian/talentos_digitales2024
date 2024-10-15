
@extends('layouts.app')

@section('content')
    <h1>Crear Obra Social</h1>
    {{-- <form action="obras_sociales/storeObraSocial" method="post"> --}}
    <form action="{{ route('obras_sociales.storeObraSocial') }}" method="post">
        @csrf
        <label for="descripcion">Descripcion</label>
        <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion') }}" > {{--  minlength="3" maxlength="100" required --}}
        @if ($errors->has('descripcion'))
            <div class="alert alert-danger">
                <p>Error {{ $errors->first('descripcion') }}</p>
            </div>
        @endif
        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}
        <br>
        <label for="numeroSeguro">Numero de Seguro</label>
        <input type="text" name="numero_seguro" id="numeroSeguro" required>
        @if ($errors->has('numero_seguro'))
            <div class="alert alert-danger">
                <p>Error {{ $errors->first('numero_seguro') }}</p>
            </div>
        @endif
        <br>
        <button type="submit">Enviar Formulario</button>

    </form>
@endsection
