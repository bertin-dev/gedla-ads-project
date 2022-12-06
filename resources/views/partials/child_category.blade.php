<li class="{{$child_category->children->isEmpty() ? "c-sidebar-nav-item" : "c-sidebar-nav-dropdown"}}">
    <a href="{{ route('folders.show', $child_category) }}" class="{{$child_category->children->isEmpty() ? "c-sidebar-nav-link" : "c-sidebar-nav-dropdown-toggle"}}">
        <i class="fa-fw fas c-sidebar-nav-icon {{$child_category->children->isEmpty() ? "fa-folder" : "fa-folder-open"}}"></i>
        {{ strtolower($child_category->name) }}
    </a>

@if ($child_category->children)
    <ul class="c-sidebar-nav-dropdown-items">
        @foreach ($child_category->children as $childCategory)
            @include('partials.child_category', ['child_category' => $childCategory])
        @endforeach
    </ul>
</li>
@endif
