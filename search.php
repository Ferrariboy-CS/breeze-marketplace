<?php
require_once __DIR__ . '/public_init.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];
$count = 0;

if ($q !== '') {
    $like = '%' . $query->validate($q) . '%';
    $sql = "SELECT p.id, p.name, p.price_old, p.price_current, p.description, c.category_name,
            (SELECT image_url FROM product_images WHERE product_id = p.id LIMIT 1) AS image_url
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.name LIKE '$like' OR p.description LIKE '$like'
            ORDER BY p.added_to_site DESC
            LIMIT 50";
    $res = $query->executeQuery($sql);
    $results = $res->fetch_all(MYSQLI_ASSOC);
    $count = $res->num_rows;
}

// Ajax mode for inline search results
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'query' => $q,
        'count' => $count,
        'results' => $results,
    ]);
    exit;
}

include './includes/header.php';
?>

<section style="padding: 20px 0;">
    <div class="container">
        <form action="search.php" method="get" style="margin-bottom:10px; display:flex; gap:8px; flex-wrap:wrap;">
            <input type="text" name="q" value="<?= htmlspecialchars($q); ?>" placeholder="Search products" style="flex:1; min-width:220px; padding:10px; border:1px solid #ddd; border-radius:6px;">
            <button type="submit" class="site-btn" style="padding:10px 18px;">Search</button>
        </form>
        <?php if ($q === ''): ?>
            <div style="color:#666;">Type something to find products.</div>
        <?php else: ?>
            <div style="margin-bottom:8px;"><strong><?= $count; ?></strong> result<?= $count === 1 ? '' : 's'; ?> for "<?= htmlspecialchars($q); ?>".</div>
            <?php if (empty($results)): ?>
                <div class="alert alert-warning" role="alert" style="padding:10px 12px; margin:0;">No products matched your search.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<section class="product spad" id="search-results">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <?php if ($q !== '' && !empty($results)): ?>
                    <div class="row">
                        <?php foreach ($results as $product): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg" data-setbg="src/images/products/<?= htmlspecialchars($product['image_url'] ?: 'placeholder.png'); ?>">
                                        <ul class="product__item__pic__hover" style="height: 20px;">
                                            <li><a href="heart.php?product_id=<?= $product['id']; ?>"><i class="fa fa-heart"></i></a></li>
                                            <li><a href="shop-details.php?product_id=<?= $product['id']; ?>"><i class="fa fa-retweet"></i></a></li>
                                            <li><a href="add_to_cart.php?product_id=<?= $product['id']; ?>"><i class="fa fa-shopping-cart"></i></a></li>
                                        </ul>
                                    </div>
                                    <div class="product__discount__item__text">
                                        <span><?= htmlspecialchars($product['category_name']); ?></span>
                                        <h5><a href="shop-details.php?product_id=<?= $product['id']; ?>"><?= htmlspecialchars($product['name']); ?></a></h5>
                                        <div class="product__item__price">N$<?= number_format($product['price_current'], 2); ?>
                                            <span>N$<?= number_format($product['price_old'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="breadcrumb-section set-bg" data-setbg="src/images/breadcrumb.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>Search</h2>
                    <div class="breadcrumb__option">
                        <a href="./">Home</a>
                        <span>Search</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include './includes/footer.php'; ?>

<?php if ($q !== ''): ?>
<script>
    // Keep results at the top on submit, then clear the query string so reload shows a fresh state.
    const resultsSection = document.getElementById('search-results');
    if (resultsSection) {
        resultsSection.scrollIntoView({ behavior: 'instant', block: 'start' });
    }
    if (window.history.replaceState) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
</script>
<?php endif; ?>
