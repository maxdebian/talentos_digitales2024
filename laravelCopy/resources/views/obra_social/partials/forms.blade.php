


    {{-- @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}
 {{--    <br> --}}

    <div class="form-group">
        <label for="descripcion">Descripcion</label>
        <input type="text" name="descripcion" id="descripcion" value="{{ old('descripcion') }}" > {{--  minlength="3" maxlength="100" required --}}
        @if ($errors->has('descripcion'))
            <div class="alert alert-danger">
                <p>Error {{ $errors->first('descripcion') }}</p>
            </div>
        @endif
      </div>
      <div class="form-group">
        <label for="numeroSeguro">Numero de Seguro</label>
        <input type="text" name="numero_seguro" id="numeroSeguro" required>
        @if ($errors->has('numero_seguro'))
            <div class="alert alert-danger">
                <p>Error {{ $errors->first('numero_seguro') }}</p>
            </div>
        @endif
      </div>
