<ul class="language-wrapper language-picker-list">
    @foreach($languages as $local => $language)
        <li class="language-item">
            <a href="{!! $language['url'] !!}" class="{!! $local == Language::current() ? 'active' : '' !!}">
                @if(!empty($language['flag']))
                    <img src="{!! $language['flag'] !!}" alt="{{$key}}" />
                @endif
                @if(!empty($language['label']))
                    <span>{!! $language['label'] !!}</span>
                @endif
            </a>
        </li>
    @endforeach
</ul>