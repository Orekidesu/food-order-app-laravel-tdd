<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Psy\Readline\Hoa\Console;

class CheckoutController extends Controller
{
    //

    public function index()
    {
        $items = Product::whereIn('id', collect(session('cart'))->pluck('id'))->get();
        $checkout_items = collect(session('cart'))->map(function ($row, $index) use ($items) {
            $qty = (int)$row['qty'];
            $cost = (float)$items[$index]->cost;
            $subtotal = $qty * $cost;

            return
                [
                    'id' => $row['id'],
                    'qty' => $row['qty'],
                    'name' => $items[$index]->name,
                    'cost' => $cost,
                    'subtotal' => round($subtotal, 2),
                    'image' => 'https://via.placeholder.com/150',
                ];
        });

        $total = $checkout_items->sum('subtotal');
        $checkout_items = $checkout_items->toArray();

        return view('checkout', compact('checkout_items', 'total'));
    }


    public function create()
    {
        $items = Product::whereIn(
            'id',
            collect(session('cart'))->pluck('id')
        )->get();
        $checkout_items = collect(session('cart'))->map(function (
            $row,
            $index
        ) use ($items) {
            $qty = (int) $row['qty'];
            $cost = (float) $items[$index]->cost;
            $subtotal = $cost * $qty;

            return [
                'id' => $row['id'],
                'qty' => $qty,
                'name' => $items[$index]->name,
                'cost' => $cost,
                'subtotal' => round($subtotal, 2),
            ];
        });

        $total = $checkout_items->sum('subtotal');

        $order = Order::create([
            'total' => $total,
        ]);

        foreach ($checkout_items as $item) {
            $order->detail()->create([
                'product_id' => $item['id'],
                'cost' => $item['cost'],
                'qty' => $item['qty'],
            ]);
        }

        return redirect('/summary');
    }


    // public function create()
    // {
    //     $items = Product::whereIn('id', collect(session('cart'))->pluck('id'))->get();
    //     $checkout_items = collect(session('cart'))->map(function ($row, $index) use ($items) {
    //         $qty = (int)$row['qty'];
    //         $cost = (float)$items[$index]->cost;
    //         $subtotal = $qty * $cost;

    //         return [
    //             'id' => $row['id'],
    //             'qty' => $row['qty'],
    //             'name' => $items[$index]->name,
    //             'cost' => $cost,
    //             'subtotal' => round($subtotal, 2),
    //             'image' => 'https://via.placeholder.com/150',
    //         ];
    //     })->toArray(); // Convert to array for use in the foreach loop

    //     $total = collect($checkout_items)->sum('subtotal');

    //     $order = Order::create(['total' => $total]);
    //     $order_details = [];
    //     foreach ($checkout_items as $item) {
    //         $order_detail = [
    //             'order_id' => $order->id,
    //             'product_id' => $item['id'],
    //             'cost' => $item['cost'],
    //             'qty' => $item['qty'],
    //         ];

    //         array_push($order_details, $order_detail);
    //     }

    //     // $order->detail()->createMany($order_details);

    //     $order_total = $order->total;
    //     return redirect()->route('summary')->with([
    //         'order_total' => $order_total,
    //         'checkout_items' => $checkout_items,
    //     ]);
    // }
    // public function summary()
    // {
    //     $order_total = session('order_total');
    //     $checkout_items = session('checkout_items');

    //     return view('summary', compact('order_total', 'checkout_items'));
    // }
}
