@if(is_array($value))
    <tr>
        <td @if(is_array($value))colspan="3"@endif>
            <h4>{{ $key }}</h4>
        </td>
    </tr>

    @foreach($value as $subKey => $subValue)
        @include('translation_manager::row', [
            'language' => $language,
            'key' => $subKey,
            'value' => $subValue,
            'parent' => isset($parent) && $parent ? "{$parent}[$key]" : $key,
            'prefix' =>  "{$prefix}{$key}.",
        ])
    @endforeach
@else
    <tr>
        <td>{{ $key }}</td>
        <td>{!! nl2br(htmlentities($value)) !!}</td>
        <td>
            <?php
            $old = $key;
            $name = $key;

            if (isset($parent) && $parent)
            {
                $name = "{$parent}[{$key}]";
                $old = preg_replace('/\[([^\]])\]/', ".$1", "{$parent}[{$key}]");
            }
            ?>
            <textarea name="{{ $name }}"
                      class="form-control"
                      rows="4">{{ old(
                          "{$old}",
                          Lang::has("{$prefix}{$key}", $language, false) ? Lang::trans("{$prefix}{$key}", [], $language) : null
                      ) }}</textarea>
        </td>
    </tr>
@endif
