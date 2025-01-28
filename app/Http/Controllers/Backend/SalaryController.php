<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvanceSalary;
use App\Models\Employee;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function AddAdvanceSalary()
    {
        $employee = Employee::latest()->get();
        return view('backend.salary.add_advance_salary',compact('employee'));
    }

    public function AdvanceSalaryStore(Request $request)
    {

        $validateData = $request->validate([
            'month' => 'required',
            'year' => 'required',
            'advance_salary' => 'required|max:255',
        ]);
        $month = $request->month;
        $employee_id = $request->employee_id;
        $advanced = AdvanceSalary::where('month',$month)->where('employee_id',$employee_id)->first();
        if ($advanced === NULL) {

            AdvanceSalary::insert([
                'employee_id' => $request->employee_id,
                'month' => $request->month,
                'year' => $request->year,
                'advance_salary' => $request->advance_salary,
                'created_at' => Carbon::now(),
            ]);
         $notification = array(
            'message' => 'Advance Salary Paid Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
        } else{
             $notification = array(
            'message' => 'Advance Already Paid',
            'alert-type' => 'warning'
        );
        return redirect()->back()->with($notification);
        }
    }

    public function AllAdvanceSalary()
    {
        $salary = AdvanceSalary::latest()->get();
        return view('backend.salary.all_advance_salary',compact('salary'));
    }

    public function EditAdvanceSalary($id){
        $employee = Employee::latest()->get();
        $salary = AdvanceSalary::findOrFail($id);
        return view('backend.salary.edit_advance_salary',compact('salary','employee'));
    }

    public function AdvanceSalaryUpdate(Request $request){
        $salary_id = $request->id;
         AdvanceSalary::findOrFail($salary_id)->update([
                'employee_id' => $request->employee_id,
                'month' => $request->month,
                'year' => $request->year,
                'advance_salary' => $request->advance_salary,
                'created_at' => Carbon::now(),
            ]);
         $notification = array(
            'message' => 'Advance Salary Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.advance.salary')->with($notification);
    }

    public function DeleteAdvanceSalary($id)
{
    $advanceSalary = AdvanceSalary::find($id);

    if (!$advanceSalary) {
        return redirect()->route('all.advance.salary')->with('error', 'Record not found.');
    }

    $advanceSalary->delete();

    return redirect()->route('all.advance.salary')->with('success', 'Advance salary record deleted successfully.');
}
}
