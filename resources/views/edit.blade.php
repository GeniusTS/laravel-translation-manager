@extends('translation_manager::layout')

@section('content')
    <h3>
        @if($namespace)
            {{ $namespace }} >
        @endif

        {{ $file }}
    </h3>

    <form method="POST" action="{{ route('translation_manager.update', [$language, $file, $namespace]) }}">
        {{ method_field('PUT') }}
        {{ csrf_field() }}
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="20%">Key</th>
                    <th width="40%">Source</th>
                    <th width="40%">Translation</th>
                </tr>
                </thead>
                <tbody>

                @foreach($translations as $key => $value)
                    @include('translation_manager::row', [
                        'key' => $key,
                        'value' => $value,
                        'language'=> $language,
                        'parent' => null,
                        'prefix' => $prefix,
                    ])
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-primary pull-right">
                    Save
                </button>
            </div>
        </div>

    </form>
@endsection