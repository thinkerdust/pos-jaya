<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Menu;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends BaseController
{
    function __construct()
    {
        $this->user = new User();
        $this->menu = new Menu();
        $this->role = new Role();
    }

    public function index()
    {
        $title = 'User Management';
        $js = 'js/apps/user-management/user.js?_='.rand();
        return view('user_management.index', compact('js', 'title'));
    }

    public function datatable_user_management(Request $request)
    {
        $data = $this->user->dataTableUser(); 
        return Datatables::of($data)->addIndexColumn()->make(true);
    }

    public function menu()
    {
        $title = 'Menu Management';
        $js = 'js/apps/user-management/menu.js?_='.rand();
        return view('user_management.menu', compact('js', 'title'));
    }

    public function datatable_menu()
    {
        $data = $this->menu->dataTableMenu();
        return Datatables::of($data)->addIndexColumn()->make(true);
    }

    public function store_menu(Request $request)
    {
        $id = $request->input('id_menu');

        $validator = Validator::make($request->all(), [
            'menu' => 'required|unique:menu, "name",'.$id,
            'parent' => 'required',
            'icon' => 'required_if:parent,0',
            'code' => 'required|unique:menu, "code",'.$id,
        ]);

        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());        
        }

        $user = Auth::user();

        $data = [
            'name' => $request->menu,
            'icon' => $request->icon,
            'parent' => $request->parent,
            'code' => $request->code,
            'flag_level' => $user->id_level
        ];

        if(!empty($id)) {
            $data['update_at'] = Carbon::now();
            $data['update_by'] = $user->id;
        }else{
            $data['insert_at'] = Carbon::now();
            $data['insert_by'] = $user->id;
        }

        $process = Menu::updateOrCreate(
            ['id' => $id],
            $data
        );

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function edit_menu(Request $request) 
    {
        $id = $request->id;
        $data = Menu::where('id', $id)->first();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_menu(Request $request)
    {
        $id = $request->id;
        $user = Auth::user();
        $process = Menu::where('id', $id)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function role()
    {
        $title = 'Role Management';
        $css_library = css_tree();
        $js_library = js_tree();
        $js = 'js/apps/user-management/role.js?_='.rand();
        return view('user_management.role', compact('js', 'js_library', 'css_library', 'title'));
    }

    public function datatable_role()
    {
        $data = $this->role->dataTableRole();
        return Datatables::of($data)->addIndexColumn()->make(true);
    }

    public function store_role(Request $request)
    {
        $id = $request->post('id_role');
        $name = Str::lower($request->post('role'));
        $flag_access = $request->except('role', 'id_role', '_token');

        $validator = Validator::make($request->all(), [
            'role' => 'required|unique:role, "name",'.$id,
        ]);

        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());        
        }

        DB::beginTransaction();
        try {

            $user = Auth::user();

            $data_role = [
                'name' => $name,
            ];

            if(!empty($id)) {
                $data_role['update_at'] = Carbon::now();
                $data_role['update_by'] = $user->id;
            }else{
                $data_role['insert_at'] = Carbon::now();
                $data_role['insert_by'] = $user->id;
            }

            DB::table('role')->updateOrInsert(
                ['id' => $id],
                $data_role
            );

            $role = Role::where('name', $name)->first();

            $data_akses = [];

            foreach ($flag_access as $key => $value) {
                $arr_akses = array(
                    'id_role' => $role->id,
                    'code_menu' => $key,
                    'flag_access' => isset($value) ? $value : 0,
                );

                $access_role = DB::table('access_role')->where(['id_role' => $role->id, 'code_menu' => $key])->first();
                if(!empty($access_role)){
                    $arr_akses['id'] = $access_role->id;
                    $arr_akses['insert_at'] = $access_role->insert_at;
                    $arr_akses['insert_by'] = $access_role->insert_by;
                    $arr_akses['update_at'] = Carbon::now();
                    $arr_akses['update_by'] = $user->id;
                }else{
                    $arr_akses['id'] = null;
                    $arr_akses['insert_at'] = Carbon::now();
                    $arr_akses['insert_by'] = $user->id;
                    $arr_akses['update_at'] = null;
                    $arr_akses['update_by'] = null;
                }

                $data_akses[] = $arr_akses;
            }

            DB::table('access_role')->upsert(
                $data_akses,
                ['id'],
                ['id_role', 'code_menu', 'flag_access' ]
            );

            DB::commit();
            return $this->ajaxResponse(true, 'Data save successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->ajaxResponse(false, 'Failed to save data', $e);
        }
    }

    public function edit_role(Request $request) 
    {
        $id = $request->id;
        $data = Role::where('id', $id)->first();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function list_permissions_menu(Request $request)
    {
        if(!$request->ajax()) {
            return abort(404);
        }

        $id = $request->get('id');
        $role = Role::where('id', $id)->first();
        $code_role = !empty($role->id) ? $role->id : 0;

        $menu = $this->menu->viewMenuTemplate('0', '0', $code_role);

        $arr = [
            'menu' => $menu,
        ];

        return response()->json($arr);
    }

    public function list_data_role(Request $request)
    {
        $q = $request->get('q');
        $data = $this->role->listDataRole($q);
        return response()->json($data);
    }
}
