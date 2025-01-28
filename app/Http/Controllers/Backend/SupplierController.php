<?php
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Carbon\Carbon;
class SupplierController extends Controller
{
    public function AllSupplier()
    {
        $supplier = Supplier::latest()->get();
        return view('backend.supplier.all_supplier',compact('supplier'));
    }

    public function AddSupplier()
    {
        return view('backend.supplier.add_supplier');
   }

    public function StoreSupplier(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|max:200',
            'email' => 'required|unique:customers|max:200',
            'phone' => 'required|max:200',
            'address' => 'required|max:400',
            'shopname' => 'required|max:200',
            'account_holder' => 'required|max:200',
            'account_number' => 'required',
            'type' => 'required',
            'image' => 'required',
        ]);

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName(); // Generate unique filename
            $file->move(public_path('upload/supplier'), $filename); // Move file to upload directory
            $save_url = 'upload/supplier/' . $filename; // Save file path
        }

        Supplier::insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'shopname' => $request->shopname,
            'type' => $request->type,
            'account_holder' => $request->account_holder,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'bank_branch' => $request->bank_branch,
            'city' => $request->city,
            'image' => $save_url,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Supplier Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.supplier')->with($notification);
    }


    public function EditSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('backend.supplier.edit_supplier',compact('supplier'));
    }

    public function UpdateSupplier(Request $request)
    {
        $supplier_id = $request->id;
        $supplier = Supplier::findOrFail($supplier_id);

        if ($request->file('image')) {
            $file = $request->file('image');

            // Delete the old image if it exists
            if ($supplier->image && file_exists(public_path($supplier->image))) {
                @unlink(public_path($supplier->image));
            }

            $filename = date('YmdHi') . $file->getClientOriginalName(); // Generate unique filename
            $file->move(public_path('upload/supplier'), $filename); // Move file to upload directory
            $supplier->image = 'upload/supplier/' . $filename; // Save file path
        }

        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        $supplier->shopname = $request->shopname;
        $supplier->type = $request->type;
        $supplier->account_holder = $request->account_holder;
        $supplier->account_number = $request->account_number;
        $supplier->bank_name = $request->bank_name;
        $supplier->bank_branch = $request->bank_branch;
        $supplier->city = $request->city;
        $supplier->save();

        $notification = array(
            'message' => 'Supplier Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.supplier')->with($notification);
    }


public function DeleteSupplier($id)
    {
        $supplier_img = Supplier::findOrFail($id);
        $img = $supplier_img->image;
        unlink($img);
        Supplier::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Supplier Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

}
