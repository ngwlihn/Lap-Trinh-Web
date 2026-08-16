<?php
// menu.php
require_once 'includes/header.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? null;
$minPrice = $_GET['min_price'] ?? 0;
$maxPrice = $_GET['max_price'] ?? 500000;

$foods = getAllFoods($search, $category, $minPrice, $maxPrice);
$categories = getCategories();
?>

<section class="hero py-4">
    <div class="container">
        <h1><i class="bi bi-book"></i> Thực Đơn Của Chúng Tôi</h1>
        <p>Hơn 20 món cơm văn phòng, bún, phở, lẩu với giá cực kỳ hợp lý</p>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="filter-section">
                    <h5><i class="bi bi-search"></i> Tìm kiếm</h5>
                    <input type="text" id="search-food" class="form-control" placeholder="Tìm món ăn..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="filter-section">
                    <h5><i class="bi bi-tag"></i> Danh Mục</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="category" value="" id="category-all" 
                               <?php echo !$category ? 'checked' : ''; ?> onchange="filterByCategory('')">
                        <label class="form-check-label" for="category-all">Tất cả</label>
                    </div>
                    <?php foreach ($categories as $cat): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="category" value="<?php echo htmlspecialchars($cat); ?>" 
                               id="category-<?php echo md5($cat); ?>" 
                               <?php echo $category == $cat ? 'checked' : ''; ?> 
                               onchange="filterByCategory('<?php echo htmlspecialchars($cat); ?>')">
                        <label class="form-check-label" for="category-<?php echo md5($cat); ?>"><?php echo htmlspecialchars($cat); ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="filter-section">
                    <h5><i class="bi bi-cash-coin"></i> Khoảng Giá</h5>
                    <div class="mb-2">
                        <label>Từ: <span id="min-price-label"><?php echo formatCurrency($minPrice); ?></span></label>
                        <input type="range" id="min-price" class="form-range" min="0" max="500000" step="10000" 
                               value="<?php echo $minPrice; ?>">
                    </div>
                    <div class="mb-2">
                        <label>Đến: <span id="max-price-label"><?php echo formatCurrency($maxPrice); ?></span></label>
                        <input type="range" id="max-price" class="form-range" min="0" max="500000" step="10000" 
                               value="<?php echo $maxPrice; ?>">
                    </div>
                    <button class="btn btn-primary w-100 mt-2" onclick="filterByPrice()">Áp dụng</button>
                    <a href="menu.php" class="btn btn-outline-secondary w-100 mt-2">Xóa lọc</a>
                </div>
            </div>
            
            <div class="col-lg-9">
                <div class="row g-4">
                    <?php if (empty($foods)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center py-5">
                            <i class="bi bi-emoji-frown" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Không tìm thấy món ăn phù hợp</h5>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($foods as $food): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card food-card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($food['category']); ?></span>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($food['name']); ?></h5>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($food['description']); ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="h5 mb-0 text-danger"><?php echo formatCurrency($food['price']); ?></span>
                                    <button class="btn btn-sm btn-primary" onclick="addToCart(<?php echo $food['id']; ?>)">
                                        <i class="bi bi-cart-plus"></i> Thêm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function filterByCategory(category) {
    let url = 'menu.php';
    const params = new URLSearchParams();
    if (category) params.append('category', category);
    const search = document.getElementById('search-food')?.value;
    if (search) params.append('search', search);
    const minPrice = document.getElementById('min-price')?.value;
    const maxPrice = document.getElementById('max-price')?.value;
    if (minPrice && minPrice > 0) params.append('min_price', minPrice);
    if (maxPrice && maxPrice < 500000) params.append('max_price', maxPrice);
    const query = params.toString();
    if (query) window.location.href = url + '?' + query;
    else window.location.href = url;
}

function filterByPrice() {
    const minPrice = document.getElementById('min-price')?.value || 0;
    const maxPrice = document.getElementById('max-price')?.value || 500000;
    let url = 'menu.php?min_price=' + minPrice + '&max_price=' + maxPrice;
    const category = document.querySelector('input[name="category"]:checked')?.value;
    if (category) url += '&category=' + encodeURIComponent(category);
    const search = document.getElementById('search-food')?.value;
    if (search) url += '&search=' + encodeURIComponent(search);
    window.location.href = url;
}

document.getElementById('search-food')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') filterByCategory(document.querySelector('input[name="category"]:checked')?.value || '');
});

document.getElementById('min-price')?.addEventListener('input', function() {
    document.getElementById('min-price-label').innerText = formatCurrency(parseInt(this.value));
});
document.getElementById('max-price')?.addEventListener('input', function() {
    document.getElementById('max-price-label').innerText = formatCurrency(parseInt(this.value));
});

function formatCurrency(amount) {
    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + "₫";
}
</script>

<?php require_once 'includes/footer.php'; ?>