<div class="card mb-4">
  <div class="card-header" style="background-color: #f8fafc;">
    <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Advanced Filters</h6>
  </div>
  <div class="card-body">
    <!-- Quick Filter Chips -->
    <div class="mb-3">
      <label class="form-label fw-bold small">Quick Filters</label>
      <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-sm btn-outline-primary quick-filter" data-filter="available">
          <i class="bi bi-check-circle"></i> Available Now
        </button>
        <button class="btn btn-sm btn-outline-primary quick-filter" data-filter="under3000">
          <i class="bi bi-currency-dollar"></i> Under ₱3,000/day
        </button>
        <button class="btn btn-sm btn-outline-primary quick-filter" data-filter="5seater">
          <i class="bi bi-people"></i> 5+ Seater
        </button>
        <button class="btn btn-sm btn-outline-primary quick-filter" data-filter="automatic">
          <i class="bi bi-gear"></i> Automatic
        </button>
        <button class="btn btn-sm btn-outline-primary quick-filter" data-filter="featured">
          <i class="bi bi-star"></i> Featured
        </button>
      </div>
    </div>

    <!-- Price Range -->
    <div class="mb-3">
      <label class="form-label fw-bold small">Price Range (per day)</label>
      <div class="row g-2">
        <div class="col-6">
          <input type="number" class="form-control form-control-sm" id="minPrice" placeholder="Min" value="0">
        </div>
        <div class="col-6">
          <input type="number" class="form-control form-control-sm" id="maxPrice" placeholder="Max" value="10000">
        </div>
      </div>
      <input type="range" class="form-range mt-2" id="priceRange" min="0" max="10000" step="500" value="10000">
      <div class="d-flex justify-content-between small text-muted">
        <span>₱0</span>
        <span id="priceDisplay">₱10,000</span>
      </div>
    </div>

    <!-- Sort Options -->
    <div class="mb-3">
      <label class="form-label fw-bold small">Sort By</label>
      <select class="form-select form-select-sm" id="sortBy">
        <option value="popular">Most Popular</option>
        <option value="price-low">Price: Low to High</option>
        <option value="price-high">Price: High to Low</option>
        <option value="rating">Highest Rated</option>
        <option value="newest">Newest First</option>
      </select>
    </div>

    <!-- Car Features -->
    <div class="mb-3">
      <label class="form-label fw-bold small">Features</label>
      <div class="form-check">
        <input class="form-check-input feature-filter" type="checkbox" id="gps" value="GPS">
        <label class="form-check-label small" for="gps">GPS Navigation</label>
      </div>
      <div class="form-check">
        <input class="form-check-input feature-filter" type="checkbox" id="bluetooth" value="Bluetooth">
        <label class="form-check-label small" for="bluetooth">Bluetooth</label>
      </div>
      <div class="form-check">
        <input class="form-check-input feature-filter" type="checkbox" id="ac" value="Air Conditioning">
        <label class="form-check-label small" for="ac">Air Conditioning</label>
      </div>
      <div class="form-check">
        <input class="form-check-input feature-filter" type="checkbox" id="usb" value="USB Port">
        <label class="form-check-label small" for="usb">USB Charging</label>
      </div>
    </div>

    <!-- Fuel Type -->
    <div class="mb-3">
      <label class="form-label fw-bold small">Fuel Type</label>
      <select class="form-select form-select-sm" id="fuelType">
        <option value="">All</option>
        <option value="Gasoline">Gasoline</option>
        <option value="Diesel">Diesel</option>
        <option value="Hybrid">Hybrid</option>
        <option value="Electric">Electric</option>
      </select>
    </div>

    <!-- Transmission -->
    <div class="mb-3">
      <label class="form-label fw-bold small">Transmission</label>
      <select class="form-select form-select-sm" id="transmission">
        <option value="">All</option>
        <option value="Automatic">Automatic</option>
        <option value="Manual">Manual</option>
      </select>
    </div>

    <div class="d-grid gap-2">
      <button class="btn btn-primary btn-sm" onclick="applyFilters()">
        <i class="bi bi-search me-2"></i>Apply Filters
      </button>
      <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">
        <i class="bi bi-arrow-clockwise me-2"></i>Reset
      </button>
    </div>
  </div>
</div>

<script>
// Price range slider
document.getElementById('priceRange').addEventListener('input', function() {
    document.getElementById('maxPrice').value = this.value;
    document.getElementById('priceDisplay').textContent = '₱' + parseInt(this.value).toLocaleString();
});

// Quick filters
document.querySelectorAll('.quick-filter').forEach(btn => {
    btn.addEventListener('click', function() {
        this.classList.toggle('active');
        applyFilters();
    });
});

function applyFilters() {
    const minPrice = parseInt(document.getElementById('minPrice').value) || 0;
    const maxPrice = parseInt(document.getElementById('maxPrice').value) || 10000;
    const sortBy = document.getElementById('sortBy').value;
    const fuelType = document.getElementById('fuelType').value;
    const transmission = document.getElementById('transmission').value;
    
    const selectedFeatures = Array.from(document.querySelectorAll('.feature-filter:checked')).map(cb => cb.value);
    const quickFilters = Array.from(document.querySelectorAll('.quick-filter.active')).map(btn => btn.dataset.filter);
    
    // Filter car items
    document.querySelectorAll('.car-item').forEach(item => {
        const price = parseFloat(item.dataset.price || 0);
        const features = (item.dataset.features || '').split(',');
        const fuel = item.dataset.fuel || '';
        const trans = item.dataset.transmission || '';
        const seats = parseInt(item.dataset.seats || 0);
        const isFeatured = item.dataset.featured === '1';
        
        let show = true;
        
        // Price filter
        if(price < minPrice || price > maxPrice) show = false;
        
        // Fuel type filter
        if(fuelType && fuel !== fuelType) show = false;
        
        // Transmission filter
        if(transmission && trans !== transmission) show = false;
        
        // Feature filters
        if(selectedFeatures.length > 0) {
            if(!selectedFeatures.every(f => features.includes(f))) show = false;
        }
        
        // Quick filters
        if(quickFilters.includes('under3000') && price >= 3000) show = false;
        if(quickFilters.includes('5seater') && seats < 5) show = false;
        if(quickFilters.includes('automatic') && trans !== 'Automatic') show = false;
        if(quickFilters.includes('featured') && !isFeatured) show = false;
        
        item.style.display = show ? 'block' : 'none';
    });
    
    // Sort cars
    sortCars(sortBy);
}

function sortCars(sortBy) {
    const container = document.getElementById('car-grid');
    const items = Array.from(container.querySelectorAll('.car-item'));
    
    items.sort((a, b) => {
        const priceA = parseFloat(a.dataset.price || 0);
        const priceB = parseFloat(b.dataset.price || 0);
        const ratingA = parseFloat(a.dataset.rating || 0);
        const ratingB = parseFloat(b.dataset.rating || 0);
        
        switch(sortBy) {
            case 'price-low': return priceA - priceB;
            case 'price-high': return priceB - priceA;
            case 'rating': return ratingB - ratingA;
            default: return 0;
        }
    });
    
    items.forEach(item => container.appendChild(item));
}

function resetFilters() {
    document.getElementById('minPrice').value = 0;
    document.getElementById('maxPrice').value = 10000;
    document.getElementById('priceRange').value = 10000;
    document.getElementById('sortBy').value = 'popular';
    document.getElementById('fuelType').value = '';
    document.getElementById('transmission').value = '';
    document.querySelectorAll('.feature-filter').forEach(cb => cb.checked = false);
    document.querySelectorAll('.quick-filter').forEach(btn => btn.classList.remove('active'));
    
    document.querySelectorAll('.car-item').forEach(item => item.style.display = 'block');
}
</script>

<style>
.quick-filter.active {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}
</style>
