<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1 maximum-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="p-token" content="{{ get_page_token() }}">

    <title>{{$title}}</title>
    <link href="{{ mix('css/app.css','6aa0e') }}" rel="stylesheet">
</head>
<body>

<div class="topbar">
    <div class="container d-flex">
        <nav class="nav d-none d-md-flex"> <!-- hidden on xs -->
            <a class="nav-link pl-0" href="javascript:void(0)"></a>
            <a class="nav-link" href="javascript:void(0)">email@email.com</a>
        </nav>
        <nav class="nav">
            <a class="nav-link pr-2 pl-0" href="javascript:void(0)"></a>
            <a class="nav-link px-2" href="javascript:void(0)"></a>
            <a class="nav-link px-2" href="javascript:void(0)"></a>
        </nav>
        <nav class="nav nav-lang ml-auto"> <!-- push it to the right -->
            <a class="nav-link active" href="javascript:void(0)">EN</a>
            <a class="nav-link pipe">|</a>
            <a class="nav-link" href="javascript:void(0)">FR</a>
        </nav>
        <ul class="nav">
            <li class="nav-item dropdown dropdown-hover">
                <a class="nav-link dropdown-toggle pr-0"
                   data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="true" aria-expanded="false">Hi, Bob</a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="media p-1 align-items-center mb-2">
                        <div class="media-body">
                            <strong>User Username</strong>
                            <div class="small counter">Point counter</div>
                        </div>
                    </div>
                    <a href="#" class="dropdown-item">My Orders</a>
                    <a href="#" class="dropdown-item">Account Setting</a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</div>
<div id="wrapper"></div>
<header>
    <div class="container">
        <a class="nav-link nav-icon ml-ni nav-toggler mr-3 d-flex d-lg-none" href="#" data-toggle="modal"
           data-target="#menuModal">
        </a>
        <a class="nav-link nav-logo" href=""><strong>App</strong></a>
        <ul class="nav nav-main d-none d-lg-flex">
            <li class="nav-item"><a class="nav-link active" href="">Home</a></li>
            <li class="nav-item dropdown dropdown-hover">
                <a class="nav-link dropdown-toggle forwardable" data-toggle="dropdown" href="#"
                   role="button" aria-haspopup="true" aria-expanded="false">Shop</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="shop-categ">Shop Categories</a>
                    <a class="dropdown-item" href="shop">Shop Grid</a>
                    <a class="dropdown-item" href="shop">Shop List</a>
                    <a class="dropdown-item" href="shop-s">Single Product</a>
                    <a class="dropdown-item" href="shop-si">Single Product v2</a>
                    <a class="dropdown-item" href=>Cart</a>
                    <a class="dropdown-item" href="shi">Checkout</a>
                </div>
            </li>
            <li class="nav-item dropdown dropdown-hover dropdown-mega">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="true" aria-expanded="false">Mega Menu</a>
                <div class="dropdown-menu">
                    <div class="row">
                        <div class="col-lg-3 border-right">
                            <div class="list-group list-group-flush list-group-no-border list-group-sm">
                                <a href="shop-grid.html"
                                   class="list-group-item list-group-item-action"><strong>Link1</strong></a>
                                <a href="shop-grid.html" class="list-group-item list-group-item-action">Link2
                                    Shoes</a>
                                <a href="shop-grid.html" class="list-group-item list-group-item-action">Link3</a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown dropdown-hover">
                <a class="nav-link dropdown-toggle forwardable" data-toggle="dropdown" href="account-profile.html"
                   role="button" aria-haspopup="true" aria-expanded="false">Account</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="account-login.html">Login / Register</a>
                    <a class="dropdown-item" href="account-profile.html">Profile Page</a>
                    <a class="dropdown-item" href="account-orders.html">Orders List</a>
                    <a class="dropdown-item" href="account-wishlist.html">Wishlist</a>
                    <a class="dropdown-item" href="account-address.html">Address</a>
                </div>
            </li>
            <li class="nav-item dropdown dropdown-hover">
                <a class="nav-link dropdown-toggle forwardable" data-toggle="dropdown" href="blog-grid.html"
                   role="button" aria-haspopup="true" aria-expanded="false">Blog</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="blog-grid.html">Post Grid</a>
                    <a class="dropdown-item" href="blog-list.html">Post List</a>
                    <a class="dropdown-item" href="blog-single.html">Single Post</a>
                </div>
            </li>
            <li class="nav-item dropdown dropdown-hover">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="true" aria-expanded="false">Pages</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="about.html">About Us</a>
                    <a class="dropdown-item" href="contact.html">Contact Us</a>
                    <a class="dropdown-item" href="compare.html">Compare</a>
                    <a class="dropdown-item" href="faq.html">Help / FAQ</a>
                    <a class="dropdown-item" href="404.html">404 Not Found</a>
                </div>
            </li>
        </ul>
    </div>
</header>

<section id="blog-featured" class="container">
    <div class="row">
        <div id="blog-featured-carousel" class="col-lg-7">
            <div id="carousel-1" class="carousel slide" data-ride="false">
                <ol class="carousel-indicators">
                    <li data-target="#carousel-1" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-1" data-slide-to="1"></li>
                    <li data-target="#carousel-1" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active" style="text-align: center;overflow:hidden">
                        <figure>
                            <img src="media/img/1.jpg"

                                 class="d-block" alt="...">
                        </figure>
                    </div>
                    <div class="carousel-item" style="text-align: center;overflow:hidden">
                        <figure>
                            <img src="media/img/2.jpg"
                                 class="d-block" alt="...">
                        </figure>
                    </div>
                    <div class="carousel-item" style="text-align: center;overflow:hidden">
                        <figure>
                            <img src="media/img/3.jpg"
                                 class="d-block" alt="...">
                        </figure>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carousel-1" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carousel-1" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
        <div id="blog-featured-right" class="col-lg-5">
            <div class="row container">
                <div class="col-lg-12">
                    <figure>
                        <img src="media/img/4.jpg">
                    </figure>
                </div>
                <div class="col-lg-6">
                    <figure>
                        <img src="media/img/5.jpg">
                    </figure>
                </div>
                <div class="col-lg-6">
                    <figure>
                        <img src="media/img/2.jpg">
                    </figure>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>


<div id="app">
    @section('content')
        <div class="container">

        </div>
    @endsection
</div>
<script>

</script>
<script src="{{ mix('js/app.js','6aa0e') }}"></script>
</body>
</html>
