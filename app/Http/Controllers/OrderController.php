<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CourierCharge;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\Product;

class OrderController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('orders.index', ['products' => $products]);
    }

    public function processOrder(Request $request)
    {
        $selectedItems = $request->input('selected_items');
        $items = Product::whereIn('id', $selectedItems)->get();

        $totalPrice = $items->sum('price');
        $totalWeight = $items->sum('weight');

        $packages = [];

        if ($totalPrice > 250) {
            $packages = $this->splitPackages($items);
        } else {
            $packages[] = [
                'items' => $items,
                'total_weight' => $totalWeight,
                'total_price' => $totalPrice,
                'courier_price' => $this->calculateCourierCharge($totalWeight),
            ];
        }

        //Optional: Save the order, packages and items into database
        $order = Order::create([
            'total_price' => $totalPrice,
            'total_weight' => $totalWeight,
        ]);

        foreach($packages as $packageData){
            $package = Package::create([
                'order_id' => $order->id,
                'total_price' => $packageData['total_price'],
                'total_weight' => $packageData['total_weight'],
                'courier_charge' => $packageData['courier_price']
            ]);

            foreach($packageData['items'] as $item){
                PackageItem::create([
                    'package_id' => $package->id,
                    'product_id' => $item->id,
                    'quantity' => 1,
                    'price' => $item->price,
                    'weight' => $item->weight
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'quantity' => 1,
                    'price' => $item->price,
                    'weight' => $item->weight
                ]);
            }
        }

        return response()->json(['packages' => $packages]);
    }

    private function splitPackages($items)
    {
        $packages = [];
        $remainingItems = $items->sortByDesc('weight')->values()->all();

        $packageIndex = 0;
        $packages[$packageIndex] = [
            'items' => [],
            'total_weight' => 0,
            'total_price' => 0,
        ];

        foreach ($remainingItems as $item) {
            if ($packages[$packageIndex]['total_price'] + $item->price < 250) {
                $packages[$packageIndex]['items'][] = $item;
                $packages[$packageIndex]['total_weight'] += $item->weight;
                $packages[$packageIndex]['total_price'] += $item->price;
            } else {
                $packageIndex++;
                $packages[$packageIndex] = [
                    'items' => [$item],
                    'total_weight' => $item->weight,
                    'total_price' => $item->price,
                ];
            }
        }

        foreach ($packages as &$package) {
            $package['courier_price'] = $this->calculateCourierCharge($package['total_weight']);
        }

        return $packages;
    }

    private function calculateCourierCharge($weight)
    {
        $charge = CourierCharge::where('min_weight', '<=', $weight)
            ->where('max_weight', '>=', $weight)
            ->value('charge');

        return $charge ?: 0;
    }
}
