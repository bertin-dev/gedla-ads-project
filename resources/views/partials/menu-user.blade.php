<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <img class="brand-title img-fluid" src="{!! url('images/logo-official.jpg') !!}" alt="" width="40" style="margin-right: 5px">
        <a class="c-sidebar-brand-full h4 white" href="{{url('/')}}">
            {{ trans('panel.site_title') }}
        </a>
    </div>


    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route("projects.index") }}">
                <i class="c-sidebar-nav-icon fas fa-fw fa-home"></i>
                {{ trans('panel.home') }}
            </a>
        </li>
        @foreach ($children_level_n as $child)
            <li class="c-sidebar-nav-dropdown">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-list"></i>
                    {{ strtolower(trans('panel.work_space')) }} ({{ strtolower($child->name) }})
                </a>
            <ul class="c-sidebar-nav-dropdown-items">
                @foreach ($child->subChildren as $childCategory)
                    @foreach($childCategory->multiUsers as $user)
                        @if($user->id==auth()->id())
                            @include('partials.child_category', ['child_category' => $childCategory])
                        @endif
                    @endforeach
                @endforeach
            </ul>
            </li>
        @endforeach

        @can('storage_access')
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ route('show-storage-document') }}">
                <i class="c-sidebar-nav-icon fas fa-fw fa-store"></i>
                {{ trans('panel.saved_documents') }}
            </a>
        </li>
        @endcan

        @can('messenger_access')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="{{ route('gedla-messenger') }}">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-send"></i>
                    {{ trans('panel.gedla-messenger') }}
                </a>
            </li>
        @endcan

        @can('calendar_access')
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="{{ route('calendar.home') }}">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-calendar"></i>
                    {{ trans('panel.gedla-calendar') }}
                </a>
            </li>
        @endcan
        <li class="c-sidebar-nav-item">
            <a href="#" class="c-sidebar-nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                </i>
                {{ trans('global.logout') }}
            </a>
        </li>
    </ul>
</div>
