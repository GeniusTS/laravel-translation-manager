@extends('translation_manager::layout')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <label for="language">
                    Language
                </label>

                <input name="language" id="language" class="form-control" placeholder="Language">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <select name="namespace" class="form-control" size="6">
                    <option value=""></option>

                    @foreach($namespaces as $namespace)
                        <option value="{{ $namespace }}">
                            {{ $namespace }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <select name="file" class="form-control" size="6"></select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <button id="submit" class="btn btn-block btn-primary">
                Submit
            </button>
        </div>
    </div>

    <script>
      var namespaceInput = $("select[name='namespace']");
      var fileInput = $("select[name='file']");
      var languageInput = $("[name='language']");

      namespaceInput.on('change', function () {
        var namespace = $("option:selected", this).val();
        fileInput.html('');

        $
          .ajax({
            type: 'GET',
            url: '{{ route('translation_manager.files') }}',
            data: {
              namespace: namespace
            },
            dataType: 'json'
          })
          .then(function (result) {
            for (var i = 0; i < result.length; i++) {
              fileInput.append('<option value="' + result[i] + '">' + result[i] + '</option>');
            }
          })
          .catch(function (error) {
            console.log(error);
          });
      });

      $("#submit").on('click', function () {
        var route = '{{ route('translation_manager.edit', ['|LANGUAGE|', '|FIlE|', '|NAMESPACE|']) }}';

        var language = languageInput.val();
        var file = $("option:selected", fileInput).val();
        var namespace = $("option:selected", namespaceInput).val();

        if (!language || !file) {
          alert("Language and File are required!");

          return;
        }

        route = route
          .replace('|LANGUAGE|', language)
          .replace('|FIlE|', file)
          .replace('|NAMESPACE|', namespace);

        window.location = route;
      });
    </script>
@endsection