<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        // Lấy các category đang active, kèm theo menu items nổi bật (featured/status active)
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Lấy các món nổi bật cho trang chủ (tối đa 6 món)
        $featuredItems = MenuItem::where('status', 'active')
            ->with('category')
            ->take(6)
            ->get();

        return view('guest.index', compact('categories', 'featuredItems'));
    }

    public function menu(Request $request)
    {
        // Lấy tất cả category đang active kèm menu items
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->with(['menuItems' => function ($query) {
                $query->where('status', 'active')->orderBy('name');
            }])
            ->get();

        // Lấy category đang được filter (nếu có)
        $selectedCategory = $request->query('category');

        // Nếu có filter theo category slug
        if ($selectedCategory) {
            $menuItems = MenuItem::where('status', 'active')
                ->whereHas('category', function ($query) use ($selectedCategory) {
                    $query->where('slug', $selectedCategory)->where('is_active', true);
                })
                ->with('category')
                ->orderBy('name')
                ->get();
        } else {
            $menuItems = MenuItem::where('status', 'active')
                ->with('category')
                ->orderBy('name')
                ->get();
        }

        return view('guest.menu', compact('categories', 'menuItems', 'selectedCategory'));
    }

    public function about()
    {
        // Trang About Us thuần frontend, không cần data từ DB
        return view('guest.aboutUs');
    }

    public function contact()
    {
        // Trang Contact thuần frontend, không cần data từ DB
        return view('guest.contact');
    }
}