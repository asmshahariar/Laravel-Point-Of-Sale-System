<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function AllCustomer()
    {
        $customer = Customer::latest()->get();
        return view('backend.customer.all_customer', compact('customer'));
    }

    public function AddCustomer()
    {
        return view('backend.customer.add_customer');
    }
    public function StoreCustomer(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|max:200',
            'email' => 'required|unique:customers|max:200',
            'phone' => 'required|max:200',
            'address' => 'required|max:400',
            'shopname' => 'required|max:200',
            'account_holder' => 'required|max:200',
            'account_number' => 'required',
            'image' => 'required',
        ]);

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName(); // Generate unique filename
            $file->move(public_path('upload/customer'), $filename); // Move file to upload directory
            $save_url = 'upload/customer/' . $filename; // Save file path
        }

        Customer::insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'shopname' => $request->shopname,
            'account_holder' => $request->account_holder,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'bank_branch' => $request->bank_branch,
            'city' => $request->city,
            'image' => $save_url,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Customer Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.customer')->with($notification);
    }

    public function EditCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        return view('backend.customer.edit_customer', compact('customer'));
    }


    public function UpdateCustomer(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|max:200',
            'email' => 'required|max:200',
            'phone' => 'required|max:200',
            'address' => 'required|max:400',
            'shopname' => 'required|max:200',
            'account_holder' => 'required|max:200',
            'account_number' => 'required',
            'city' => 'required|max:200',
        ]);

        // Fetch the customer record
        $customer_id = $request->id;
        $customer = Customer::findOrFail($customer_id);

        // Update customer details
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->shopname = $request->shopname;
        $customer->account_holder = $request->account_holder;
        $customer->account_number = $request->account_number;
        $customer->bank_name = $request->bank_name;
        $customer->bank_branch = $request->bank_branch;
        $customer->city = $request->city;

        // Handle image upload
        if ($request->file('image')) {
            $file = $request->file('image');

            // Delete the old image if it exists
            if ($customer->image && file_exists(public_path($customer->image))) {
                @unlink(public_path($customer->image));
            }

            $filename = date('YmdHi') . $file->getClientOriginalName(); // Generate unique filename

            // Move the file to the upload directory
            $file->move(public_path('upload/customer'), $filename);

            $customer->image = 'upload/customer/' . $filename;
        }

        $customer->save();

        $notification = array(
            'message' => 'Customer Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.customer')->with($notification);
    }
    public function DeleteCustomer($id)
    {
        $customer_img = Customer::findOrFail($id);
        $img = $customer_img->image;
        unlink($img);
        Customer::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Customer Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method

}
