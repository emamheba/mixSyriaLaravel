<?php

namespace App\Http\Controllers\Api\Frontend\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Backend\Listing;
use App\Models\Backend\Comment;
use App\Models\ListingRefresh;
use Carbon\Carbon;

class DashboardStatisticsController extends Controller
{
    /**
     * Provide statistics for the authenticated user's dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();
        
        // جلب معرفات (IDs) جميع إعلانات المستخدم
        $listingIds = Listing::where('user_id', $user->id)->pluck('id');

        // --- 1. حساب بيانات البطاقات الإحصائية ---

        // إجمالي المشاهدات على جميع الإعلانات
        $totalViews = Listing::where('user_id', $user->id)->sum('view');
        
        // إجمالي الاستفسارات (سنعتبر التعليقات هي الاستفسارات)
        $totalInquiries = Comment::whereIn('listing_id', $listingIds)->count();

        // الإعلانات النشطة
        $activeListingsCount = Listing::where('user_id', $user->id)
                                    ->where('status', 1) // مفعل من الإدارة
                                    ->where('is_published', 1) // منشور من قبل المستخدم
                                    ->count();
        
        // الحد الأقصى للإعلانات من باقة الاشتراك
        $listingLimit = $user->activeMembership->listing_limit ?? 0;

        // حساب التغيرات مقارنة بالشهر الماضي
        $statsChanges = $this->calculateMonthlyChanges($listingIds);

        // حساب معدل التحويل (الاستفسارات / المشاهدات)
        $conversionRate = ($totalViews > 0) ? ($totalInquiries / $totalViews) * 100 : 0;


        // --- 2. إعداد بيانات مخطط المشاهدات (لآخر 6 أشهر) ---
        // بما أن لا يوجد جدول لتتبع المشاهدات اليومية، سنستخدم "تحديثات الإعلانات" كمؤشر للنشاط
        $monthlyActivity = $this->getMonthlyActivity($listingIds);

        
        // --- 3. إعداد بيانات مخطط أداء الإعلانات (أفضل 5 إعلانات) ---
        $adPerformance = Listing::where('user_id', $user->id)
            ->withCount('comments') // لجلب عدد الاستفسارات (التعليقات)
            ->orderBy('view', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($listing) {
                return [
                    'name'      => $listing->title,
                    'views'     => (int) $listing->view,
                    'inquiries' => (int) $listing->comments_count,
                ];
            });


        // --- تجميع البيانات النهائية في استجابة JSON ---
        return response()->json([
            'summary' => [
                'views' => [
                    'total' => $totalViews,
                    'change' => round($statsChanges['views_change'], 2),
                ],
                'inquiries' => [
                    'total' => $totalInquiries,
                    'change' => round($statsChanges['inquiries_change'], 2),
                ],
                'active_listings' => [
                    'count' => $activeListingsCount,
                    'limit' => $listingLimit,
                ],
                'conversion_rate' => [
                    'rate' => round($conversionRate, 2),
                    'change' => round($statsChanges['conversion_rate_change'], 2),
                ],
            ],
            'views_chart' => $monthlyActivity,
            'performance_chart' => $adPerformance,
        ]);
    }

    /**
     * حساب التغيرات في الإحصائيات مقارنة بالشهر الماضي
     */
    private function calculateMonthlyChanges($listingIds)
    {
        // تحديد فترات زمنية: الشهر الحالي والشهر الماضي
        $startOfThisMonth = now()->startOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        // الاستفسارات
        $inquiriesThisMonth = Comment::whereIn('listing_id', $listingIds)->where('created_at', '>=', $startOfThisMonth)->count();
        $inquiriesLastMonth = Comment::whereIn('listing_id', $listingIds)->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        
        // المشاهدات (باستخدام تحديثات الإعلان كمؤشر)
        $viewsThisMonth = ListingRefresh::whereIn('listing_id', $listingIds)->where('refreshed_at', '>=', $startOfThisMonth)->count();
        $viewsLastMonth = ListingRefresh::whereIn('listing_id', $listingIds)->whereBetween('refreshed_at', [$startOfLastMonth, $endOfLastMonth])->count();

        // حساب نسبة التغير
        $inquiriesChange = ($inquiriesLastMonth > 0) ? (($inquiriesThisMonth - $inquiriesLastMonth) / $inquiriesLastMonth) * 100 : ($inquiriesThisMonth > 0 ? 100 : 0);
        $viewsChange = ($viewsLastMonth > 0) ? (($viewsThisMonth - $viewsLastMonth) / $viewsLastMonth) * 100 : ($viewsThisMonth > 0 ? 100 : 0);
        
        // حساب نسبة التغير لمعدل التحويل
        $conversionRateThisMonth = ($viewsThisMonth > 0) ? ($inquiriesThisMonth / $viewsThisMonth) * 100 : 0;
        $conversionRateLastMonth = ($viewsLastMonth > 0) ? ($inquiriesLastMonth / $viewsLastMonth) * 100 : 0;
        $conversionRateChange = $conversionRateThisMonth - $conversionRateLastMonth;


        return [
            'inquiries_change' => $inquiriesChange,
            'views_change' => $viewsChange,
            'conversion_rate_change' => $conversionRateChange
        ];
    }
    
    /**
     * جلب بيانات النشاط الشهري لآخر 6 أشهر
     */
    private function getMonthlyActivity($listingIds)
    {
        // إعداد اللغة العربية لأسماء الشهور
        Carbon::setLocale('ar');
        
        $startDate = now()->subMonths(5)->startOfMonth();

        // جلب عدد التحديثات مجمعة حسب الشهر والسنة بكويري واحد لتحسين الأداء
        $refreshesByMonth = ListingRefresh::whereIn('listing_id', $listingIds)
            ->where('refreshed_at', '>=', $startDate)
            ->selectRaw('DATE_FORMAT(refreshed_at, "%Y-%m") as month_year, COUNT(*) as count')
            ->groupBy('month_year')
            ->get()
            ->keyBy('month_year');
        
        $monthlyStats = [];

        // المرور على آخر 6 أشهر وتعبئة البيانات
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $monthName = $date->translatedFormat('F'); // ex: "يونيو"
            
            $monthlyStats[] = [
                'name' => $monthName,
                // في الواجهة الأمامية، هذا الحقل اسمه 'views'
                'views' => $refreshesByMonth->get($key)->count ?? 0,
            ];
        }
        
        return $monthlyStats;
    }
}