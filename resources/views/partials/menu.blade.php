<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <img class="brand-title img-fluid" src="{!! url('images/logo-official.jpg') !!}" alt="" width="40"
             style="margin-right: 5px">
        <a class="c-sidebar-brand-full h4" href="#">
            {{ trans('panel.site_title') }}
        </a>
    </div>

    <ul class="c-sidebar-nav">

        <li class="c-sidebar-nav-item">
            <select class="searchable-field form-control">

            </select>
        </li>

        <li class="c-sidebar-nav-item">
            <a href="{{ route("admin.home") }}" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fas fa-fw fa-home">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>


        <li class="c-sidebar-nav-dropdown">
            <a class="c-sidebar-nav-dropdown-toggle" href="#">
                <i class="fa-fw fas fa-cogs c-sidebar-nav-icon"></i>
                {{ trans('panel.settings') }}
            </a>

            <ul class="c-sidebar-nav-dropdown-items">

                @can('user_management_access')
                    <li class="c-sidebar-nav-dropdown">
                        <a class="c-sidebar-nav-dropdown-toggle" href="#">
                            <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                            </i>
                            {{ trans('cruds.userManagement.title') }}
                        </a>
                        <ul class="c-sidebar-nav-dropdown-items">
                            @can('permission_access')
                                <li class="c-sidebar-nav-item">
                                    <a href="{{ route("admin.permissions.index") }}"
                                       class="c-sidebar-nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "active" : "" }}">
                                        <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                        </i>
                                        {{ trans('cruds.permission.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="c-sidebar-nav-item">
                                    <a href="{{ route("admin.roles.index") }}"
                                       class="c-sidebar-nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "active" : "" }}">
                                        <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                        </i>
                                        {{ trans('cruds.role.title') }}
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="c-sidebar-nav-item">
                                    <a href="{{ route("admin.users.index") }}"
                                       class="c-sidebar-nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "active" : "" }}">
                                        <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                        </i>
                                        {{ trans('cruds.user.title') }}
                                    </a>
                                </li>
                            @endcan

                            @can('audit_log_access')
                                <li class="c-sidebar-nav-item">
                                    <a href="{{ route("admin.audit-logs.index") }}"
                                       class="c-sidebar-nav-link {{ request()->is("admin/audit-logs") || request()->is("admin/audit-logs/*") ? "active" : "" }}">
                                        <i class="fa-fw fas fa-file-alt c-sidebar-nav-icon">

                                        </i>
                                        {{ trans('cruds.auditLog.title') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('project_access')
                    <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.projects.index") }}"
                           class="c-sidebar-nav-link {{ request()->is("admin/projects") || request()->is("admin/projects/*") ? "active" : "" }}">
                            <i class="fa-fw fas fa-hospital-o c-sidebar-nav-icon">

                            </i>
                            {{ trans('cruds.project.title') }}
                        </a>
                    </li>
                @endcan

                @can('folder_access')
                    <li class="c-sidebar-nav-dropdown">
                        <a class="c-sidebar-nav-dropdown-toggle" href="#">
                            <i class="fa-fw fas fa-folder-open c-sidebar-nav-icon"></i>
                            {{ trans('cruds.folder_access.folder_management') }}
                        </a>
                        <ul class="c-sidebar-nav-dropdown-items">
                            <li class="c-sidebar-nav-item">
                                <a href="{{ route("admin.folders.index") }}"
                                   class="c-sidebar-nav-link {{ request()->is("admin/folders") || request()->is("admin/folders/*") ? "active" : "" }}">
                                    <i class="fa-fw fas fa-folder c-sidebar-nav-icon">

                                    </i>
                                    {{ trans('cruds.folder.title') }}
                                </a>
                            </li>
                            <li class="c-sidebar-nav-item">
                                <a href="{{ route("admin.folders_access.index") }}"
                                   class="c-sidebar-nav-link {{ request()->is("admin/folders_access") || request()->is("admin/folders_access/*") ? "active" : "" }}">
                                    <i class="fa-fw fas fa-key c-sidebar-nav-icon"></i>
                                    {{ trans('cruds.folder_access.access_folder') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @can('workflow_validation_management_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.workflow-management.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/workflow_management") || request()->is("admin/workflow_management/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa fa-send c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.workflow_management.title') }}
                            </a>
                        </li>
                @endcan

                @can('archive_file_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.archive.index") }}" class="c-sidebar-nav-link">
                                <i class="c-sidebar-nav-icon fas fa-fw fa-save">

                                </i>
                                {{ trans('global.archive_document') }}
                            </a>
                        </li>
                @endcan

                @can('user_alert_access')
                    <li class="c-sidebar-nav-item">
                        <a href="{{ route("admin.user-alerts.index") }}"
                           class="c-sidebar-nav-link {{ request()->is("admin/user-alerts") || request()->is("admin/user-alerts/*") ? "active" : "" }}">
                            <i class="fa-fw fas fa-bell c-sidebar-nav-icon">

                            </i>
                            {{ trans('cruds.userAlert.title') }}
                        </a>
                    </li>
                @endcan

                @can('signature_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.signature.index") }}"
                               class="c-sidebar-nav-link {{ request()->is("admin/signature") || request()->is("admin/signature/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-pencil-square c-sidebar-nav-icon"></i>
                                {{ trans('cruds.signature.title') }}
                            </a>
                        </li>
                @endcan

                @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                    @can('profile_password_edit')
                        <li class="c-sidebar-nav-item">
                            <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}"
                               href="{{ route('profile.password.edit') }}">
                                <i class="fa-fw fas fa-key c-sidebar-nav-icon">
                                </i>
                                {{ trans('global.change_password') }}
                            </a>
                        </li>
                    @endcan
                @endif

            </ul>
        </li>

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
            <a class="c-sidebar-nav-link" href="{{ route('admin.calendar.admin') }}">
                <i class="c-sidebar-nav-icon fas fa-fw fa-calendar"></i>
                {{ trans('panel.gedla-calendar') }}
            </a>
        </li>
        @endcan

        <li class="c-sidebar-nav-item">
            <a href="#" class="c-sidebar-nav-link"
               onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                </i>
                {{ trans('global.logout') }}
            </a>
        </li>
    </ul>

</div>
