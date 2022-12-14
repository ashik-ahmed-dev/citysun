<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ApprovedOrdersExport;
use App\Exports\ClosedOrdersExport;
use App\Exports\OrdersExport;
use App\Exports\PendingOrdersExport;
use App\Exports\RunningOrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class OrdersController extends Controller
{
    public function index(){
        $title = 'all orders';
        $orders = Order::latest()->get();
        return view('admin.orders.index', compact('orders', 'title'));
    }

    public function new_order(){
        $title = 'New order';
        $services = Service::latest()->get();
        return view('admin.orders.order_form', compact('title', 'services'));
    }

    public function store(Request $request){

        $request->validate([
            'service_id' => 'required',
            'service_price' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        Order::create([
            'user_id' => Auth::user()->id,
            'service_id' => $request->service_id,
            'service_price' => $request->service_price,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'note' => $request->note,
            'status' => 'Approved',
            'updated_at' => Carbon::now(),
        ]);
        return redirect()->route('admin.order.approved')->with('success', 'Order created successfully');
    }

    public function pending(){
        $title = 'pending orders';
        $orders = Order::pending()->latest()->get();
        return view('admin.orders.pending', compact('orders', 'title'));
    }
    public function edit($id){
        $title = 'edit orders';
        $data = Order::FindOrFail($id);
        return view('admin.orders.update', compact('data', 'title'));
    }

    public function update(Request $request, $id){

        Order::FindOrFail($id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'note' => $request->note,
            'service_price' => $request->service_price,
            'type' => $request->type,
            'payment_number' => $request->payment_number,
            'TrxID' => $request->TrxID,
            'status' => $request->status,
            'updated_at' => Carbon::now(),
        ]);
        return redirect()->route('admin.order.approved')->with('success', 'Order updated successfully');
    }


    public function approved(){
        $title = 'approved orders';
        $orders = Order::approved()->latest()->get();
        return view('admin.orders.approved', compact('orders', 'title'));
    }

    public function approved_update($id){
        Order::FindOrFail($id)->update([
            'status' => 'Running',
            'updated_at' => Carbon::now(),
        ]);
        return redirect()->route('admin.order.running')->with('success', 'Order approved successfully');
    }


    public function running(){
        $title = 'running orders';
        $orders = Order::running()->latest()->get();
        return view('admin.orders.running', compact('orders', 'title'));
    }

    public function running_update($id){
        Order::FindOrFail($id)->update([
            'status' => 'Closed',
            'updated_at' => Carbon::now(),
        ]);
        return redirect()->route('admin.order.closed')->with('success', 'Order Closed successfully');
    }

    public function closed(){
        $title = 'closed orders';
        $orders = Order::closed()->latest()->get();
        return view('admin.orders.closed', compact('orders', 'title'));
    }

    public function delete($id){
        Order::FindOrFail($id)->delete();
        return redirect()->route('admin.order')->with('success', 'Order deleted successfully');
    }

    public function print($id){
        $order = Order::with('service')->where('id',$id)->first();
        return view('admin.orders.invoice', compact('order'));
    }


    public function pdf_download($id){
        $order = Order::with('service')->where('id',$id)->first();
        $pdf = Pdf::loadView('admin.orders.pdf', compact('order'));
        return $pdf->download('invoice.pdf');
    }

    public function show($id){
        $title = 'Order Details';
        $order = Order::findOrFail($id);
        return view('admin.orders.view', compact('order', 'title'));
    }

    public function export()
    {
        return Excel::download(new OrdersExport, 'orders.xlsx');
    }

    public function exportPending()
    {
        return Excel::download(new PendingOrdersExport(), 'pending-orders.xlsx');
    }

    public function exportApproved(){
        return Excel::download(new ApprovedOrdersExport(), 'approved-orders.xlsx');
    }

    public function exportRunning(){
        return Excel::download(new RunningOrdersExport(), 'running-orders.xlsx');
    }

    public function exportClosed(){
        return Excel::download(new ClosedOrdersExport(), 'closed-orders.xlsx');
    }

}
