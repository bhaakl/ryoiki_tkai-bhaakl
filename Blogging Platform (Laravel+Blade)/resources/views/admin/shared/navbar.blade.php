<nav class="navbar fixed-top navbar-expand-lg" data-bs-theme="dark" style="background-color: #06095c">
    <div class="container" >
        <!-- Branding Image -->
        <a href="{{ route('home') }}" class="navbar-brand">
            {{ config('app.name', 'Laravel') }}
        </a>

        <!-- Collapsed Hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarCollapse">


            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink" style="background-color: #06095c">
                        <a href="{{ route('users.show', Auth::user()) }}" class="dropdown-item">
                            @lang('users.public_profile')
                        </a>

                        <a href="{{ route('users.edit') }}" class="dropdown-item">
                            @lang('users.settings')
                        </a>

                        <a href="{{ route('admin.posts.index') }}" class="dropdown-item">
                            @lang('users.mng_post')
                        </a>

                        <div class="dropdown-divider"></div>

                        <a href="{{ url('/logout') }}"
                            class="dropdown-item"
                            onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                            @lang('auth.logout')
                        </a>

                        <form id="logout-form" class="d-none" action="{{ url('/logout') }}" method="POST">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

