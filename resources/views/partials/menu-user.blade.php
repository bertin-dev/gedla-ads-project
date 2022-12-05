<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <img class="brand-title img-fluid" src="{!! url('images/logo-official.jpg') !!}" alt="" width="40" style="margin-right: 5px">
        <a class="c-sidebar-brand-full h4 white" href="{{url('/')}}">
            {{ trans('panel.site_title') }}
        </a>
    </div>


    <ul class="c-sidebar-nav">
        {{--<li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="#">
                <i class="c-sidebar-nav-icon fas fa-fw fa-home"></i>
                {{ trans('panel.work_space') }}
            </a>
        </li>--}}
        @foreach ($children_level_n as $child)
            <li class="c-sidebar-nav-dropdown">

                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-home"></i>
                    {{ strtolower(trans('panel.work_space')) }} ({{ strtolower($child->name) }})
                </a>

            <ul class="c-sidebar-nav-dropdown-items">
                @foreach ($child->subChildren as $childCategory)
                    @include('partials.child_category', ['child_category' => $childCategory])
                @endforeach
            </ul>
            </li>
            <li class="c-sidebar-nav-item">
                <a href="#" class="c-sidebar-nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                    </i>
                    {{ trans('global.logout') }}
                </a>
            </li>
        @endforeach
    </ul>

    {{--<ul class="c-sidebar-nav">
        @foreach ($projects as $project)
            <li class="c-sidebar-nav-dropdown">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-home"></i>
                    {{ trans('panel.work_space') }} ({{ $project->name ?? '' }})
                </a>

                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.projects.index") }}" class="c-sidebar-nav-link">
                            <i class="fa-fw fas fa-upload c-sidebar-nav-icon">

                            </i>
                            {{ trans('panel.acquisition_document') }}
                        </a>
                    </li>

                    <li class="c-sidebar-nav-dropdown">
                        <a class="c-sidebar-nav-dropdown-toggle" href="#">
                            <i class="c-sidebar-nav-icon fas fa-fw fa-home"></i>
                            Folder {{ $folder->name }}
                        </a>
                    @foreach ($folder->children as $myFolder)

                        @if($myFolder->children->isEmpty())

                                    <ul class="c-sidebar-nav-dropdown-items">
                                        <li class="c-sidebar-nav-items">

                                            <ul class="c-sidebar-nav-dropdown-items">
                                                <li class="c-sidebar-nav-item">
                                                    <a href="#" class="c-sidebar-nav-link">
                                                        <i class="fa-fw fas fa-upload c-sidebar-nav-icon"></i>
                                                        PHP
                                                    </a>
                                                </li>

                                                <li class="c-sidebar-nav-item">
                                                    <a href="#" class="c-sidebar-nav-link">
                                                        <i class="fa-fw fas fa-upload c-sidebar-nav-icon"></i>
                                                        HTML / CSS
                                                    </a>

                                                </li>
                                            </ul>
                                        </li>

                                    </ul>


                        @endif
                            <ul class="c-sidebar-nav-dropdown-items">
                                <li class="c-sidebar-nav-item">
                                    <a href="{{ route('folders.show', [$myFolder]) }}" class="c-sidebar-nav-link {{ request()->is("admin/projects") || request()->is("admin/projects/*") ? "active" : "" }}">
                                        <i class="fa-fw fas fa fa-folder-open c-sidebar-nav-icon">

                                        </i>
                                        {{ $myFolder->name }}
                                    </a>
                                </li>
                            </ul>
                    @endforeach
                        </li>


                        <li class="c-sidebar-nav-dropdown">
                        <a class="c-sidebar-nav-dropdown-toggle" href="#">
                            <i class="c-sidebar-nav-icon fas fa-fw fa-home"></i>
                            {{ trans('panel.work_space') }}
                        </a>
                        <ul class="c-sidebar-nav-dropdown-items">
                            <li class="c-sidebar-nav-item">
                                <a href="{{ route("admin.projects.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/projects") || request()->is("admin/projects/*") ? "active" : "" }}">
                                    <i class="fa-fw fas fa-hospital-o c-sidebar-nav-icon">

                                    </i>
                                    {{ trans('cruds.project.title') }}
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </li>
        @endforeach
    </ul>--}}

</div>
