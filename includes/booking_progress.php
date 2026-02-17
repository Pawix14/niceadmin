<?php
function renderBookingProgress($status) {
    $steps = [
        'Pending' => ['label' => 'Pending', 'icon' => 'clock', 'color' => '#f59e0b'],
        'Confirmed' => ['label' => 'Confirmed', 'icon' => 'check-circle', 'color' => '#3b82f6'],
        'Active' => ['label' => 'Active', 'icon' => 'car-front', 'color' => '#10b981'],
        'Completed' => ['label' => 'Completed', 'icon' => 'flag', 'color' => '#8b5cf6']
    ];
    
    $order = ['Pending', 'Confirmed', 'Active', 'Completed'];
    $current_index = array_search($status, $order);
    if($status == 'Cancelled') $current_index = -1;
    
    echo '<div class="booking-progress mb-3">';
    echo '<div class="d-flex justify-content-between position-relative" style="padding: 0 20px;">';
    
    // Progress line
    $progress_width = $current_index >= 0 ? ($current_index / (count($order) - 1)) * 100 : 0;
    echo '<div style="position: absolute; top: 20px; left: 20px; right: 20px; height: 3px; background: #e5e7eb; z-index: 0;"></div>';
    echo '<div style="position: absolute; top: 20px; left: 20px; width: calc(' . $progress_width . '% - 40px); height: 3px; background: #3b82f6; z-index: 0; transition: width 0.3s;"></div>';
    
    foreach($order as $index => $step_status) {
        $step = $steps[$step_status];
        $is_active = $index <= $current_index;
        $is_current = $step_status == $status;
        
        echo '<div class="text-center" style="z-index: 1;">';
        echo '<div style="width: 40px; height: 40px; border-radius: 50%; background: ' . ($is_active ? $step['color'] : '#e5e7eb') . '; display: flex; align-items: center; justify-content: center; margin: 0 auto; transition: all 0.3s; ' . ($is_current ? 'box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);' : '') . '">';
        echo '<i class="bi bi-' . $step['icon'] . '" style="color: white; font-size: 18px;"></i>';
        echo '</div>';
        echo '<small class="mt-2 d-block fw-bold" style="color: ' . ($is_active ? $step['color'] : '#9ca3af') . ';">' . $step['label'] . '</small>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
}
?>
