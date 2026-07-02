<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

class SystemController extends Controller
{
    public function __construct(
        private Order           $order,
        private Admin           $admin,
    )
    {
    }


    /**
     * @return JsonResponse
     */
    public function restaurant_data(): JsonResponse
    {
        $new_order = $this->order->where(['checked' => 0])->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_order' => $new_order]
        ]);
    }

    /**
     * @return Renderable
     */
    public function settings(): Renderable
    {
        return view('admin-views.settings');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function settings_update(Request $request): RedirectResponse
    {
        $request->validate([
            'f_name' => 'required',
            'email' => ['required', 'unique:admins,email,' . auth('admin')->id() . ',id'],
            'phone' => 'required',
        ], [
            'f_name.required' => translate('First name is required!'),
        ]);

        $admin = $this->admin->find(auth('admin')->id());
        if (!$admin) {
            Toastr::error(translate('Admin not found!'));
            return back();
        }

        $admin->f_name = $request->f_name;
        $admin->l_name = $request->l_name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->image = $request->has('image') ? Helpers::update('admin/', $admin->image, 'png', $request->file('image')) : $admin->image;
        $admin->save();

        Toastr::success(translate('Admin updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function settings_password_update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);
        $admin = $this->admin->find(auth('admin')->id());
        if (!$admin) {
            Toastr::error(translate('Admin not found!'));
            return back();
        }

        $admin->password = bcrypt($request['password']);
        $admin->save();

        Toastr::success(translate('Admin password updated successfully!'));
        return back();
    }

}
