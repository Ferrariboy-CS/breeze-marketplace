<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
$cartItems = [];
$total_price = 0;

if (isset($_SESSION['loggedin']) && isset($_SESSION['id'])) {
    $cartItems = $query->getCartItems($_SESSION['id']);
    $total_price = array_reduce($cartItems, function ($total, $item) {
        return $total + $item['total_price'];
    }, 0);
}

function countTable($table)
{
    global $query;
    if (!isset($_SESSION['id'])) {
        return 0;
    }
    $userId = $_SESSION['id'];
    $result = $query->executeQuery("SELECT COUNT(*) AS total_elements FROM $table WHERE user_id = $userId");
    $row = $result->fetch_assoc();
    return $row['total_elements'];
}
?>

<div class="humberger__menu__overlay"></div>
<div class="humberger__menu__wrapper">
    <div class="humberger__menu__logo">
        <a href="./"><img src="./src/images/breezelogo.png" alt="Breeze Marketplace" style="max-height:130px; width:auto;"></a>
    </div>
    <div class="humberger__menu__cart">
        <ul>
            <li><a href="./heart.php"><i class="fa fa-heart"></i> <span><?= countTable('wishes'); ?></span></a>
            </li>
            <li><a href="./shoping-cart.php"><i class="fa fa-shopping-bag"></i>
                    <span><?= countTable('cart'); ?></span></a></li>
        </ul>
        <div class="header__cart__price">Total: <span>N$<?= number_format($total_price, 2); ?></span></div>
    </div>
    <div class="humberger__menu__widget">
        <div class="header__top__right__auth">
            <?php if (!empty($_SESSION['loggedin'])): ?>
                <a href="#" onclick="logout()"><i class="fa fa-user"></i>Logout</a>
            <?php else: ?>
                <a href="./login/"><i class="fa fa-user"></i>Login</a>
            <?php endif; ?>
        </div>
    </div>
    <nav class="humberger__menu__nav mobile-menu">
        <ul>
            <li>
                <a href="./" class="<?= ($currentPage == 'index.php') ? 'active' : ''; ?>">Home</a>
            </li>

            <li>
                <a href="./heart.php" class="<?= ($currentPage == 'heart.php') ? 'active' : ''; ?>">Wish
                    List</a>
            </li>

            <?php if (!empty($_SESSION['loggedin'])): ?>
                <li>
                    <a href="./orders.php" class="<?= ($currentPage == 'orders.php') ? 'active' : ''; ?>">Orders</a>
                </li>
            <?php endif; ?>

            <li>
                <a href="./shoping-cart.php"
                    class="<?= ($currentPage == 'shoping-cart.php') ? 'active' : ''; ?>">Cart</a>
            </li>
        </ul>
    </nav>
    <div id="mobile-menu-wrap"></div>
    <div class="header__top__right__social">
        <a href="#"><i class="fa fa-facebook"></i></a>
        <a href="#"><i class="fa fa-twitter"></i></a>
        <a href="#"><i class="fa fa-linkedin"></i></a>
        <a href="#"><i class="fa fa-pinterest-p"></i></a>
    </div>
    <div class="humberger__menu__contact">
        <ul>
            <li><i class="fa fa-envelope"></i> support@breezemarketplace.com </li>
            <li>Free Shipping for all Orders over N$500</li>
        </ul>
    </div>
</div>

<header class="header">
    <div class="header__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="header__top__left">
                        <ul>
                            <li><i class="fa fa-envelope"></i> support@breezemarketplace.com </li>
                            <li>Free Shipping for all Orders over N$500</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="header__top__right">
                        <div class="header__top__right__social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-linkedin"></i></a>
                            <a href="#"><i class="fa fa-pinterest-p"></i></a>
                        </div>
                        <div class="header__top__right__auth">
                            <?php if (!empty($_SESSION['loggedin'])): ?>
                                <a href="#" onclick="logout()"><i class="fa fa-user"></i>Logout</a>
                            <?php else: ?>
                                <a href="./login/"><i class="fa fa-user"></i>Login</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="header__logo">
                    <a href="./"><img src="./src/images/breezelogo.png" alt="Breeze Marketplace" style="max-height:130px; width:auto;"></a>
                </div>
            </div>
            <div class="col-lg-6">
                <nav class="header__menu">
                    <ul>
                        <li>
                            <a href="./" class="<?= ($currentPage == 'index.php') ? 'active' : ''; ?>">Home</a>
                        </li>

                        <li>
                            <a href="./heart.php" class="<?= ($currentPage == 'heart.php') ? 'active' : ''; ?>">Wish
                                List</a>
                        </li>

                        <?php if (!empty($_SESSION['loggedin'])): ?>
                            <li>
                                <a href="./orders.php" class="<?= ($currentPage == 'orders.php') ? 'active' : ''; ?>">Orders</a>
                            </li>
                        <?php endif; ?>

                        <li>
                            <a href="./shoping-cart.php"
                                class="<?= ($currentPage == 'shoping-cart.php') ? 'active' : ''; ?>">Cart</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="col-lg-3">
                <div class="header__cart">
                    <ul>
                        <li><a href="./heart.php"><i class="fa fa-heart"></i>
                                <span><?= countTable('wishes'); ?></span></a></li>
                        <li><a href="./shoping-cart.php"><i class="fa fa-shopping-bag"></i>
                                <span><?= countTable('cart'); ?></span></a></li>
                    </ul>
                    <div class="header__cart__price">Total: <span>N$<?= number_format($total_price, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="humberger__open">
            <i class="fa fa-bars"></i>
        </div>
    </div>
</header>

<style>
    .hero__categories__all:after {
        content: '';
        display: block;
    }

    ul li a.active {
        color: #7fad39 !important;
        font-weight: bold !important;
    }
</style>

<div id="search-overlay" style="display:none; position:fixed; top:0; left:0; right:0; background:#fff; z-index:9999; max-height:70vh; overflow:auto; box-shadow:0 4px 20px rgba(0,0,0,0.15); padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:10px;">
        <div id="search-overlay-title" style="font-weight:700; font-size:16px;">Search results</div>
        <button type="button" id="search-overlay-close" style="border:0; background:#7fad39; color:#fff; padding:6px 10px; border-radius:4px; cursor:pointer;">Close</button>
    </div>
    <div id="search-overlay-content"></div>
</div>

<section class="hero hero-normal" style="margin-bottom: -50px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="hero__categories">
                    <div class="hero__categories__all">
                        <i class="fa fa-bars"></i>
                        <span>Category</span>
                    </div>
                    <ul>
                        <?php
                        $categories = $query->select('categories', '*');
                        foreach ($categories as $category): ?>
                            <li>
                                <a
                                    href="category.php?category=<?= $category['id']; ?>"><?= $category['category_name'] ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="hero__search">
                    <div class="hero__search__form">
                        <form id="global-search-form" action="search.php" method="get">
                            <div class="hero__search__categories">
                                All Categories
                            </div>
                            <input type="text" name="q" id="global-search-input" placeholder="What do you need?" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                            <button type="submit" class="site-btn">SEARCH</button>
                        </form>
                    </div>
                    <div class="hero__search__phone">
                        <div class="hero__search__phone__icon">
                            <i class="fa fa-phone"></i>
                        </div>
                        <div class="hero__search__phone__text">
                            <h5>+264 85 811 2457</h5>
                            <span>support 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function logout() {
        Swal.fire({
            title: 'Are you sure you want to log out?',
            text: "You cannot undo this action!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, log out!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = './logout/';
            }
        });
    }
</script>

<script>
    (function () {
        const form = document.getElementById('global-search-form');
        const input = document.getElementById('global-search-input');
        const overlay = document.getElementById('search-overlay');
        const overlayContent = document.getElementById('search-overlay-content');
        const overlayTitle = document.getElementById('search-overlay-title');
        const overlayClose = document.getElementById('search-overlay-close');

        if (!form || !input || !overlay || !overlayContent || !overlayTitle || !overlayClose) {
            return;
        }

        const formatPrice = (value) => {
            const num = Number(value);
            return isNaN(num) ? '0.00' : num.toFixed(2);
        };

        const renderResults = (data) => {
            overlayTitle.textContent = data.query ? `Results for "${data.query}" (${data.count})` : 'Search results';
            if (!data.query) {
                overlayContent.innerHTML = '<div style="color:#666;">Type something to find products.</div>';
                return;
            }
            if (!data.results || data.results.length === 0) {
                overlayContent.innerHTML = '<div class="alert alert-warning" style="padding:10px 12px; margin:0;">No products matched your search.</div>';
                return;
            }
            const cards = data.results.map((p) => {
                const img = p.image_url ? p.image_url : 'placeholder.png';
                return `
                    <div class="col-lg-3 col-md-4 col-sm-6" style="margin-bottom:16px;">
                        <div class="product__item">
                            <div class="product__item__pic" style="background-image:url('src/images/products/${img}'); background-size:cover; background-position:center;">
                                <ul class="product__item__pic__hover" style="height: 20px;">
                                    <li><a href="heart.php?product_id=${p.id}"><i class="fa fa-heart"></i></a></li>
                                    <li><a href="shop-details.php?product_id=${p.id}"><i class="fa fa-retweet"></i></a></li>
                                    <li><a href="add_to_cart.php?product_id=${p.id}"><i class="fa fa-shopping-cart"></i></a></li>
                                </ul>
                            </div>
                            <div class="product__discount__item__text">
                                <span>${p.category_name || ''}</span>
                                <h5><a href="shop-details.php?product_id=${p.id}">${p.name}</a></h5>
                                <div class="product__item__price">N$${formatPrice(p.price_current)} <span>N$${formatPrice(p.price_old)}</span></div>
                            </div>
                        </div>
                    </div>`;
            }).join('');
            overlayContent.innerHTML = `<div class="row">${cards}</div>`;
        };

        const openOverlay = () => {
            overlay.style.display = 'block';
            overlay.scrollTop = 0;
        };

        const closeOverlay = () => {
            overlay.style.display = 'none';
        };

        overlayClose.addEventListener('click', closeOverlay);

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const query = input.value.trim();
            const url = `search.php?ajax=1&q=${encodeURIComponent(query)}`;
            fetch(url)
                .then((res) => res.json())
                .then((data) => {
                    renderResults(data);
                    openOverlay();
                })
                .catch(() => {
                    overlayContent.innerHTML = '<div class="alert alert-danger" style="padding:10px 12px; margin:0;">Search failed. Please try again.</div>';
                    openOverlay();
                });
        });
    })();
</script>