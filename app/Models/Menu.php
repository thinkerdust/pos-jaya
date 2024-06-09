<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'menu';
    protected $fillable = [
        'id',
        'code',
        'parent',
        'name',
        'icon',
        'insert_at',
        'insert_by',
        'update_at',
        'update_by',
        'status'
    ];

    public function dataTableMenu()
    {
        $query = DB::table('menu')->select('id', 'code', 'parent', 'name', 'icon', 'status');
        
        return $query;
    }

    public function viewMenuTemplate($parent = '0', $level = '0', $code_role = '')
    {

        if (empty($code_role)) {
            $result = $this->menuTemplate($parent);
        } else {
            $result = $this->menuTemplateByRole($parent, $code_role);
        }

        $arr = array();

        if (!empty($result)) {
            foreach ($result as $row => $val) {
                $id_menu = $val->code;

                if (empty($code_role)) {
                    // Semua action, default-nya unchecked saat akan membuat role baru.
                    $state_readOnly = '';
                    $state_fullAccess = '';
                    $state_noAccess = '';
                } else {
                    $state_readOnly = ($val->flag_access === 0) ? 'checked' : '';
                    $state_fullAccess = ($val->flag_access === 1) ? 'checked' : '';
                    $state_noAccess = ($val->flag_access === 9) ? 'checked' : '';
                }

                $id_readOnly = 'ro_' . $id_menu;
                $id_fullAccess = 'fa_' . $id_menu;
                $id_noAccess = 'na_' . $id_menu;

                $chk_readOnly = $this->custom_checkbox($id_readOnly, $id_menu, 0, $state_readOnly, 'Read Only');
                $chk_fullAccess = $this->custom_checkbox($id_fullAccess, $id_menu, 1, $state_fullAccess, 'Full Access');
                $chk_noAccess = $this->custom_checkbox($id_noAccess, $id_menu, 9, $state_noAccess, 'No Access');

                $action =  '<div class="g-3 align-center flex-wrap">' . $chk_readOnly . $chk_fullAccess . $chk_noAccess . '</div>';

                $arr[$row] = array(
                    'text'  => $val->name,
                    'id'    => $id_menu
                );

                $icon = $val->icon;

                if (!empty($icon)) {
                    $arr[$row]['icon'] = $icon;
                } else {
                    $arr[$row]['icon'] = "icon ni ni-menu-circled";
                }


                if (!empty($val->parent) || $val->hitung == 0) {
                    $arr[$row]['data']['action'] = $action;
                }

                if (empty($code_role)) {

                    if ($val->hitung == 0) {
                        $arr[$row]['state'] = array(
                            'opened' => true
                        );
                    }
                } else {

                    if ($val->checked == 1 && $val->hitung == 0) {
                        $arr[$row]['state'] = array(
                            'selected'  => true,
                            'opened'    => true
                        );
                    }
                }

                if ($val->hitung > 0) {
                    $arr[$row]['children'] = $this->viewMenuTemplate($id_menu, $level + 1, $code_role);
                }
            }
        }

        return $arr;
    }

    public function menuTemplate($parent = '0')
    {
        $user = Auth::user();

        $sql = "SELECT a.*, IFNULL(jumlah_menu.jumlah, 0) AS hitung
                FROM menu a
                    LEFT JOIN (
                        SELECT parent, COUNT(*) AS jumlah
                        FROM menu
                        GROUP BY parent
                    ) AS jumlah_menu ON a.parent = jumlah_menu.parent
                WHERE a.parent = '$parent' AND a.status = 1
                ";

        $data = DB::select($sql);
        return $data;
    }

    public function menuTemplateByRole($parent = '0', $code_role = '')
    {
        $sql = "SELECT a.*, IFNULL(jumlah_menu.jumlah, 0) AS hitung,
                    CASE WHEN (c.code_menu <> '') 
                        THEN TRUE 
                        ELSE FALSE 
                    END AS checked,
                    c.flag_access
                FROM menu a
                LEFT JOIN (
                    SELECT parent, COUNT(*) AS jumlah
                    FROM menu
                    GROUP BY parent
                ) AS jumlah_menu ON a.code = jumlah_menu.parent
                LEFT JOIN (
                    SELECT code_menu, flag_access
                    FROM access_role 
                    WHERE id_role = '$code_role'
                ) AS c ON c.code_menu = a.code
                WHERE a.parent = '$parent' AND a.status = 1";

        $data = DB::select($sql);
        return $data;
    }

    public function custom_checkbox($id, $name = '', $value = '', $state = '', $label_text = '')
    {

        $name_attribute = ($name != '') ? 'name="' . $name . '"' : '';
        $value_attribute = ($value != '') ? 'value="' . $value . '"' : '';

        $checkbox = '<div class="g">
                        <div class="custom-control custom-control-sm custom-radio">
                            <input type="radio" class="custom-control-input" '
            . $name_attribute . ' id="' . $id . '" ' . $value_attribute . ' ' . $state . '>
                            <label class="custom-control-label" for="' . $id . '">' . $label_text . '</label>
                        </div>
                    </div>';

        return $checkbox;
    }

    public function menu()
    {
        $user = Auth::user();
        $header_menu = DB::select("SELECT m.parent, m.code, m.name, m.icon
        from menu m 
        join (
            select m.parent as code
            from users u 
            join access_role ta on u.id_role = ta.id_role 
            join menu m on ta.code_menu = m.code 
            where ta.flag_access != 9 and u.id = $user->id
            group by m.parent 
        ) sq on m.code = sq.code");

        $menu = '';
        foreach($header_menu as $row) {
            $menu .= '<li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon '.$row->icon.'"></em></span>
                            <span class="nk-menu-text">'.$row->name.'</span>
                        </a>
                        <ul class="nk-menu-sub">';

            $detail_menu = DB::select("SELECT m.parent, m.code, m.name, m.url
            from users u 
            join access_role ta on u.id_role = ta.id_role 
            join menu m on ta.code_menu = m.code 
            where ta.flag_access != 9 and u.id = $user->id and m.parent = '$row->code' ");

            foreach($detail_menu as $key) {
                $menu .= '<li class="nk-menu-item">
                            <a href="'.$key->url.'" class="nk-menu-link"><span class="nk-menu-text">'.$key->name.'</span></a>
                        </li>';
            }

            $menu .= '</ul>';
        }

        return $menu;
    }
}
