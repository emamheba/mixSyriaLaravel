{{-- <div class="modal fade" id="shareProject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-share-project modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('orders.appointDriver') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center">
                        <h4 class="mb-2">{{__('Appoint Driver')}}</h4>
                        <p>Appoint driver for product orders</p>
                    </div>
                </div>
                
                <!-- Hidden input for order_id -->
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <div class="mb-6 mx-4 mx-md-0">
                    <label for="driverSelect" class="form-label">{{__('Select driver')}}</label>
                    <select id="driverSelect" name="driver_id" class="form-select form-select-lg share-project-select" data-allow-clear="true" required>
                        @foreach ($drivers as $driver)
                            <option value="{{$driver->id}}">{{$driver->name}}</option>
                        @endforeach
                    </select>
                </div>

                <h5 class="ms-4 ms-md-0">{{$drivers->count()}} {{__('Drivers Available')}}</h5>
                
                <ul class="p-0 m-0 mx-4 mx-md-0">
                    @foreach ($drivers as $driver)
                    <li class="d-flex flex-wrap mb-4">
                        <div class="avatar me-4">
                            <img src="{{asset($driver->image)}}" alt="avatar" class="rounded-circle">
                        </div>
                        <div class="d-flex justify-content-between flex-grow-1">
                            <div class="me-2">
                                <p class="mb-0 text-heading">{{$driver->name}}</p>
                                <p class="small mb-0">{{$driver->phone .", ".$driver->address}}</p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>

                <div class="d-flex align-items-start mt-6 align-items-sm-center mx-4 mx-md-0 mb-4 mb-md-0">
                    <div class="d-flex justify-content-between flex-grow-1 align-items-center flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-link ti-xs me-2"></i>{{__('Appoint Selected Driver')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> --}}


<div class="modal fade" id="shareProject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content modern-modal">
        <form action="{{ route('orders.appointDriver') }}" method="POST">
          @csrf
          <input type="hidden" name="order_id" value="{{ $order->id }}">
          
          <!-- Modal Header -->
          <div class="modal-header glass-effect">
            <div class="header-content">
              <h2 class="modal-title">🚚 تعيين سائق للتوصيل</h2>
              <div class="search-container">
                <input type="text" id="driverSearch" placeholder="ابحث عن سائق بالاسم أو الهاتف..." class="search-input">
                <i class="fas fa-search search-icon"></i>
              </div>
            </div>
            <button type="button" class="btn-close neo-close" data-bs-dismiss="modal"></button>
          </div>
  
          <!-- Modal Body -->
          <div class="modal-body compact-list">
            <!-- Drivers List -->
            <div class="driver-list-container">
              @foreach ($drivers as $driver)
              <div class="driver-item" 
                   data-driver-id="{{ $driver->id }}"
                   data-name="{{ $driver->name }}"
                   data-phone="{{ $driver->phone }}"
                   data-vehicle="{{ $driver->vehicle_type }}"
                   onclick="selectDriver(this)">
                <div class="driver-main">
                  <div class="driver-avatar">
                    <img src="{{ asset($driver->image) }}" alt="{{ $driver->name }}">
                    <div class="driver-status {{ $driver->available ? 'available' : 'busy' }}">
                      {{ $driver->available ? 'متاح' : 'مشغول' }}
                    </div>
                  </div>
                  <div class="driver-info">
                    <h3 class="driver-name">{{ $driver->name }}</h3>
                    <div class="driver-details">
                      <span class="vehicle-type"><i class="fas fa-motorcycle"></i> {{ $driver->vehicle_type }}</span>
                      <span class="driver-phone"><i class="fas fa-phone"></i> {{ $driver->phone }}</span>
                    </div>
                    <div class="driver-rating">
                      <div class="stars">★★★★☆</div>
                      <span class="delivery-count">({{ $driver->completed_orders }} توصيلة)</span>
                    </div>
                  </div>
                </div>
                <div class="selection-indicator">
                  <div class="check-circle"></div>
                </div>
              </div>
              @endforeach
            </div>
            
            <input type="hidden" name="driver_id" id="selectedDriverId" required>
          </div>
  
          <!-- Modal Footer -->
          <div class="modal-footer glass-effect">
            <button type="button" class="btn neo-btn cancel-btn" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn neo-btn confirm-btn" disabled>
              <span class="btn-text">تأكيد الاختيار</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <style>
  .modern-modal {
    border-radius: 15px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
  }
  
  .modal-header {
    padding: 1rem;
    border-bottom: 1px solid #eee;
  }
  
  .search-container {
    position: relative;
    width: 100%;
    margin-top: 1rem;
  }
  
  .search-input {
    width: 100%;
    padding: 0.8rem 2.5rem;
    border-radius: 25px;
    border: 1px solid #ddd;
    font-size: 0.9rem;
    transition: all 0.3s;
  }
  
  .search-input:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 8px rgba(76,175,80,0.2);
  }
  
  .search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
  }
  
  .driver-list-container {
    max-height: 60vh;
    overflow-y: auto;
    padding-right: 0.5rem;
  }
  
  .driver-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    margin: 0.5rem 0;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    transition: all 0.2s;
    cursor: pointer;
  }
  
  .driver-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  .driver-main {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
  }
  
  .driver-avatar {
    position: relative;
    width: 50px;
    height: 50px;
  }
  
  .driver-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
  }
  
  .driver-status {
    position: absolute;
    bottom: -5px;
    right: -5px;
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    color: white;
  }
  
  .driver-status.available {
    background: #4CAF50;
  }
  
  .driver-status.busy {
    background: #ff5252;
  }
  
  .driver-info {
    flex: 1;
  }
  
  .driver-name {
    margin: 0;
    font-size: 1rem;
    color: #333;
  }
  
  .driver-details {
    display: flex;
    gap: 1rem;
    margin: 0.3rem 0;
    font-size: 0.85rem;
    color: #666;
  }
  
  .selection-indicator {
    width: 24px;
    height: 24px;
    border: 2px solid #ddd;
    border-radius: 50%;
    margin-left: 1rem;
    transition: all 0.2s;
  }
  
  .driver-item.selected {
    background: #f8fff8;
    border: 1px solid #4CAF50;
  }
  
  .driver-item.selected .selection-indicator {
    border-color: #4CAF50;
    background: #4CAF50;
  }
  
  .driver-item.selected .check-circle {
    display: block;
  }
  .confirm-btn {
    background: #7367f0 !important;
  }
  .confirm-btn .btn-text {
    color: #ffffff !important;
  }
  .confirm-btn:disabled {
    background: #5257afa8 !important;
    cursor: not-allowed;
  }
  </style>
  
  <script>
  function selectDriver(item) {
    // إلغاء التحديد السابق
    document.querySelectorAll('.driver-item').forEach(d => d.classList.remove('selected'));
    
    // تحديد العنصر الجديد
    item.classList.add('selected');
    
    // تفعيل زر التأكيد
    document.querySelector('.confirm-btn').disabled = false;
    
    // تعيين القيمة المختارة
    document.getElementById('selectedDriverId').value = item.dataset.driverId;
  }
  
  // وظيفة البحث
  document.getElementById('driverSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    document.querySelectorAll('.driver-item').forEach(item => {
      const name = item.dataset.name.toLowerCase();
      const phone = item.dataset.phone;
      const vehicle = item.dataset.vehicle.toLowerCase();
      
      const matches = name.includes(searchTerm) || 
                     phone.includes(searchTerm) || 
                     vehicle.includes(searchTerm);
      
      item.style.display = matches ? 'flex' : 'none';
    });
  });
  </script>