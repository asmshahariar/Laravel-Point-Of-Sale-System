<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function AllEmployee()
    {
        $employee = Employee::latest()->get();
        return view('backend.employee.all_employee', compact('employee'));
    }

    public function AddEmployee()
    {
        return view('backend.employee.add_employee');
    }

    public function StoreEmployee(Request $request)
    {
        $validateData = $request->validate(
            [
                'name' => 'required|max:200',
                'email' => 'required|unique:employees|max:200',
                'phone' => 'required|max:200',
                'address' => 'required|max:400',
                'salary' => 'required|max:200',
                'vacation' => 'required|max:200',
                'experience' => 'required',
                'image' => 'required',
            ],
            [
                'name.required' => 'This Employee Name Field Is Required',
            ]
        );

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi') . $file->getClientOriginalName(); // Generate unique filename
            $file->move(public_path('upload/employee'), $filename); // Move file to upload directory
            $save_url = 'upload/employee/' . $filename; // Save file path
        }

        Employee::insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'experience' => $request->experience,
            'salary' => $request->salary,
            'vacation' => $request->vacation,
            'city' => $request->city,
            'image' => $save_url,
            'created_at' => Carbon::now(),
        ]);
        $notification = array(
            'message' => 'Employee Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.employee')->with($notification);
    }

    public function EditEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        return view('backend.employee.edit_employee', compact('employee'));
    }

    public function UpdateEmployee(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|max:200',
            'email' => 'required|max:200',
            'phone' => 'required|max:200',
            'address' => 'required|max:400',
            'experience' => 'required|max:200',
            'salary' => 'required|max:200',
            'vacation' => 'required|max:200',
            'city' => 'required|max:200',
        ]);

        // Fetch the employee record
        $employee_id = $request->id;
        $employee = Employee::findOrFail($employee_id);

        // Update employee details
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->address = $request->address;
        $employee->experience = $request->experience;
        $employee->salary = $request->salary;
        $employee->vacation = $request->vacation;
        $employee->city = $request->city;

        // Handle image upload
        if ($request->file('image')) {
            $file = $request->file('image');

            // Delete the old image if it exists
            if ($employee->image && file_exists(public_path($employee->image))) {
                @unlink(public_path($employee->image));
            }


            $filename = date('YmdHi') . $file->getClientOriginalName();

            // Move the file to the upload directory
            $file->move(public_path('upload/employee'), $filename);


            $employee->image = 'upload/employee/' . $filename;
        }


        $employee->save();


        $notification = array(
            'message' => 'Employee Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.employee')->with($notification);
    }

    public function DeleteEmployee($id)
    {
        $employee_img = Employee::findOrFail($id);
        $img = $employee_img->image;
        unlink($img);
        Employee::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Employee Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method
}
