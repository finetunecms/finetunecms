<aside class="col-md-2 sidemenu" id="vue-sidemenu">
        <header class="col-xs-12 header">
            <h4 class="identity"><a title="Link to {{ trans('finetune::main.admin') }}" href="/admin"><img class="logo" src="/finetune/assets/img/identity-white.svg"/><span>finetune</span></a></h4>
        </header>

        <nav>
            <ul>
                @if(auth()->user()->ability(['Superadmin'], ['can_manage_types']))
                    <li><a href="/admin/types" @if(Request::segment(2) == 'types') class="active" @endif title="{{ trans('finetune::main.sidemenu.types') }}"><i class="ic ic-type"></i><span>{{ trans('finetune::main.sidemenu.types') }}</span></a></li>
                @endif
                @if(auth()->user()->ability(['Superadmin'], ['can_manage_sites']))
                    <li><a href="/admin/sites" @if(Request::segment(2) == 'sites') class="active" @endif title="{{ trans('finetune::main.sidemenu.sites') }}"><i class="ic ic-site"></i> <span>{{ trans('finetune::main.sidemenu.sites') }}</span></a></li>
                @endif
                @if(auth()->user()->ability(['Superadmin'], ['can_manage_content']))
                    <li><a href="/admin/content" @if(Request::segment(2) == 'content') class="active" @endif title="{{ trans('finetune::main.sidemenu.content') }}"><i class="ic ic-content"></i><span>{{ trans('finetune::main.sidemenu.content') }}</span></a></li>
                @endif
                @if(auth()->user()->ability(['Superadmin'], ['can_manage_media']))
                    <li><a href="/admin/media" @if(Request::segment(2) == 'media') class="active" @endif title="{{ trans('finetune::main.sidemenu.media') }}"><i class="ic ic-media"></i><span>{{ trans('finetune::main.sidemenu.media') }}</span></a></li>
                @endif
                @if(auth()->user()->ability(['Superadmin'], ['can_manage_tags']))
                    <li><a href="/admin/tags" @if(Request::segment(2) == 'tags') class="active" @endif title="{{ trans('finetune::main.sidemenu.tags') }}"><i class="ic ic-tags"></i><span>{{ trans('finetune::main.sidemenu.tags') }}</span></a></li>
                @endif
                @if(auth()->user()->ability(['Superadmin'], ['can_manage_snippets']))
                    <li><a href="/admin/snippets" @if(Request::segment(2) == 'snippets') class="active" @endif title="{{ trans('finetune::main.sidemenu.snippets') }}"><i class="ic ic-snips"></i><span>{{ trans('finetune::main.sidemenu.snippets') }}</span></a>
                @endif
                @if(auth()->user()->ability(['Superadmin'], ['can_manage_users']))
                    <li><a href="/admin/users" @if(Request::segment(2) == 'users') class="active" @endif title="{{ trans('finetune::main.sidemenu.users') }}"><i class="ic ic-user"></i><span>{{ trans('finetune::main.sidemenu.users') }}</span></a></li>
                @endif
                @if(auth()->user()->ability(['Superadmin'], ['can_manage_plugins']))
                    @if(!empty(config('packages')['main-nav']))
                        <li style="border-top:1px solid #FFF">
                            @foreach(config('packages')['main-nav'] as $navItem)
                                @include($navItem)
                            @endforeach
                        </li>
                    @endif
                @endif
            </ul>
        </nav>

        <footer class="footer">
            <a title="Hide Menu" href="#" @click='hideSidemenu()' ><i
                    class="fa fa-angle-right @if(session('small') != 'false') fa-angle-left @endif"
                    id="sidemenu-arrow"></i></a>
        </footer>

</aside>
