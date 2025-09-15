<?php

namespace App\Services\Listing;

use App\Exceptions\ListingRefreshException;
use App\Models\Backend\Listing;
use App\Models\ListingRefresh;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ListingRefreshService
{
    private const REFRESH_INTERVAL_DAYS = 7;
    
    public function refresh(Listing $listing): bool
    {
        return DB::transaction(function () use ($listing) {
            $this->validateRefreshEligibility($listing);
            
            $this->updateListingTimestamp($listing);
            $this->recordRefreshActivity($listing);
            
            return true;
        });
    }
    
    public function canRefresh(Listing $listing): bool
    {
        try {
            $this->validateRefreshEligibility($listing);
            return true;
        } catch (ListingRefreshException $e) {
            return false;
        }
    }
    
    public function getNextRefreshDate(Listing $listing): ?Carbon
    {
        $lastRefresh = $this->getLastRefresh($listing);
        
        if (!$lastRefresh) {
            return null;
        }
        
        return $lastRefresh->refreshed_at->addDays(self::REFRESH_INTERVAL_DAYS);
    }
    
    public function getDaysUntilNextRefresh(Listing $listing): ?int
    {
        $nextRefreshDate = $this->getNextRefreshDate($listing);
        
        if (!$nextRefreshDate) {
            return 0;
        }
        
        $daysRemaining = now()->diffInDays($nextRefreshDate, false);
        
        return max(0, ceil($daysRemaining));
    }
    
    private function validateRefreshEligibility(Listing $listing): void
    {
        $this->validateUserMembership($listing->user);
        $this->validateRefreshInterval($listing);
    }
    
    private function validateUserMembership($user): void
    {
        if (!moduleExists('Membership')) {
            throw new ListingRefreshException(__('Membership module not available'));
        }
        
        $membership = $user->membershipUser;
        
        if (!$membership) {
            throw new ListingRefreshException(__('Active membership required to refresh listings'));
        }
        
        if ($membership->expire_date < now()) {
            throw new ListingRefreshException(__('Your membership has expired. Please renew to refresh listings'));
        }
    }
    
    private function validateRefreshInterval(Listing $listing): void
    {
        $lastRefresh = $this->getLastRefresh($listing);
        
        if (!$lastRefresh) {
            return;
        }
        
        $daysSinceLastRefresh = $lastRefresh->refreshed_at->diffInDays(now());
        
        if ($daysSinceLastRefresh < self::REFRESH_INTERVAL_DAYS) {
            $daysRemaining = self::REFRESH_INTERVAL_DAYS - $daysSinceLastRefresh;
            throw new ListingRefreshException(
                __('You can refresh this listing in :days days', ['days' => $daysRemaining])
            );
        }
    }
    
    private function updateListingTimestamp(Listing $listing): void
    {
        $listing->update(['updated_at' => now()]);
    }
    
    private function recordRefreshActivity(Listing $listing): void
    {
        ListingRefresh::create([
            'listing_id' => $listing->id,
            'user_id' => $listing->user_id,
            'refreshed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    private function getLastRefresh(Listing $listing): ?ListingRefresh
    {
        return ListingRefresh::where('listing_id', $listing->id)
            ->latest('refreshed_at')
            ->first();
    }
}