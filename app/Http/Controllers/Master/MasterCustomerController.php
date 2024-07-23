<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class MasterCustomerController extends BaseController
{
    function __construct()
    {
        $this->customer = new Customer();
    }

    public function index()
    {
        $title = 'Master Pelanggan';
        $js = 'js/apps/master/customer.js?_='.rand();
        return view('master.customer', compact('js', 'title'));
    }

    public function datatable_customer(Request $request)
    {
        $data = $this->customer->dataTableCustomer();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '';
                if(Gate::allows('crudAccess', 'MD2', $row)) {
                    if($row->status == 1) {
                        $btn = '<a class="btn btn-dim btn-outline-secondary btn-sm" onclick="edit(\'' . $row->uid . '\')"><em class="icon ni ni-edit"></em><span>Edit</span></a>
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                            ';
                    }
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store_customer(Request $request)
    {
        $uid = $request->input('uid');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'organisation' => 'required',
            'phone' => 'required|numeric',
            'type' => 'required'
        ]);

        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());        
        }

        $user = Auth::user();

        $data = [
            'name' => $request->name,
            'organisation' => $request->organisation,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'type' => $request->type
        ];

        if(!empty($uid)) {
            $data['update_at'] = Carbon::now();
            $data['update_by'] = $user->id;
        }else{
            $data['insert_at'] = Carbon::now();
            $data['insert_by'] = $user->id;
            $uid_customer = 'C'.Carbon::now()->format('YmdHisu');
            $data['uid'] = $uid_customer;
        }

        $process = DB::table('customer')->updateOrInsert(
            ['uid' => $uid],
            $data
        );

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function edit_customer(Request $request) 
    {
        $uid = $request->uid;
        $data = Customer::where('uid', $uid)->first();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_customer(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('customer')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function list_data_customer(Request $request)
    {
        $q = $request->get('q');
        $data = $this->customer->listDataCustomer($q);
        return response()->json($data);
    }
}
