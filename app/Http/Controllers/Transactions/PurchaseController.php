<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->purchase_order = new PurchaseOrder();
    }

    public function index()
    {
        $title = 'Pembelian';
        $js = 'js/apps/transactions/purchase_order.js?_=' . rand();
        return view('transactions.purchase_order.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function datatable_purchase_order(Request $request)
    {
        $data = $this->purchase_order->dataTablePurchaseOrders();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if (Gate::allows('crudAccess', 'TX1', $row)) {
                    if ($row->status == 1) {
                        $btn = '<a href="/transaction/purchase?uid=' . $row->uid . '" class="btn btn-dim btn-outline-secondary btn-sm"><em class="icon ni ni-edit"></em><span>Edit</span></a>&nbsp;
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(' . $row->uid . ')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                            ';
                    }
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function add_purchase_order(Request $request)
    {
        $uid = $request->uid;
        $js = 'js/apps/transactions/purchase_order.js?_=' . rand();
        return view('transactions.purchase_order.add_purchase_order', compact('js', 'uid'));
    }

    public function store_purchase_order(Request $request)
    {
        $uid = $request->input('uid_purchase_order');

        $validator = Validator::make($request->all(), [
            'supplier' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->ajaxResponse(false, $validator->errors()->first());
        }

        DB::beginTransaction();
        try {

            $user = Auth::user();

            $data = [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ];

            if (!empty($uid)) {
                $data['update_at'] = Carbon::now();
                $data['update_by'] = $user->username;
            } else {
                $data['insert_at'] = Carbon::now();
                $data['insert_by'] = $user->username;
                $uid_company = 'C' . Carbon::now()->format('YmdHisu');
                $data['uid'] = $uid_company;
            }

            // remove old photo
            if (!empty($uid) && $request->file('photo')) {
                $data_company = Company::where('uid', $uid)->first();
                $oldFile = $data_company->photo;

                if (!empty($oldFile)) {
                    if (Storage::disk('public')->exists($oldFile)) {
                        // Delete the file
                        Storage::disk('public')->delete($oldFile);
                    }
                }

            }

            // upload photo
            if ($request->file('photo')) {

                $file = $request->file('photo');
                $fileName = $file->getClientOriginalName();
                $fileName = str_replace(' ', '', $fileName);

                // Define a file path
                $filePath = 'uploads/company/photo/' . uniqid() . '_' . $fileName;

                // Store the file in the local storage
                $upload = Storage::disk('public')->put($filePath, file_get_contents($file));
                if ($upload) {
                    $data['photo'] = $filePath;
                }
            }

            DB::table('company')->updateOrInsert(
                ['uid' => $uid],
                $data
            );

            DB::commit();
            return $this->ajaxResponse(true, 'Data save successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->ajaxResponse(false, 'Failed to save data', $e);
        }
    }

    public function edit_company(Request $request)
    {
        $uid = $request->uid;
        $data = Company::where('uid', $uid)->first();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_company(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('company')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->username]);

        if ($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function list_data_company(Request $request)
    {
        $q = $request->get('q');
        $data = $this->company->listDataCompany($q);
        return response()->json($data);
    }
}
