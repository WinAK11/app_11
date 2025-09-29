@php
    function active($route)
    {
        return request()->routeIs($route) ? 'menu-link_active' : '';
    }
@endphp

<ul class="account-nav">
    <li><a href="{{ route('user.index') }}" class="menu-link menu-link_us-s {{ active('user.index') }}">Dashboard</a></li>
    <li><a href="{{ route('user.orders') }}"
            class="menu-link menu-link_us-s {{ request()->is('account-orders*') ? 'menu-link_active' : '' }}">Orders</a>
    </li>
    <li><a href="account-address.html"
            class="menu-link menu-link_us-s {{ request()->is('account-address*') ? 'menu-link_active' : '' }}">Addresses</a>
    </li>
    <li><a href="{{ route('user.account.detail') }}"
            class="menu-link menu-link_us-s {{ active('user.account.detail') }}">Account Details</a></li>
    <li><a href="{{ route('wishlist.index')}}"
            class="menu-link menu-link_us-s {{ request()->is('account-wishlist*') ? 'menu-link_active' : '' }}">Wishlist</a>
    </li>
    <li>
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <a href="{{ route('logout') }}" class="menu-link menu-link_us-s"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
        </form>
    </li>
</ul>
