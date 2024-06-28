<div class="language-wrapper dropdown">
    <div class="language-picker-selected d-flex gap-2 align-items-center" data-bs-toggle="dropdown">
        @if(!empty($languages[$current]['flag']))
            <img src="{!! $languages[$current]['flag'] !!}" alt="{{$key}}" />
        @endif
        @if(!empty($languages[$current]['label']))
            <span>{!! $languages[$current]['label'] !!}</span>
        @endif
            <i class="fa-solid fa-caret-down"></i>
    </div>
    <ul class="language-picker-select dropdown-menu dropdown-menu-arrow dropdown-menu-end">
        @foreach($languages as $local => $language)
            <li class="language-item">
                <a href="{!! $language['url'] !!}">
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
</div>
