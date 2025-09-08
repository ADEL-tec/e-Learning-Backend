<?php

namespace App\Admin\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class OrderController extends AdminController
{
    protected $title = 'Order';

    protected function grid()
    {
        $grid = new Grid(new Order());

        $grid->column('id', __('Id'));
        $grid->column('user_token', __('Buyer'))->display(function ($token) {
            return User::where('token', '=', $token)->value('name');
        });
        $grid->column('total_amount', __('Total amount'));
        $grid->column('course_id', __('Course'))->display(function ($id) {
            return Course::where('id', '=', $id)->value('name');
        });
        $grid->column('status', __('Status'))->display(function ($status) {
            return $status == "1" ? "Payed" : "Pending";
        });
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->disableActions();
        $grid->disableCreateButton();
        // $grid->disableExport();
        //$grid->disableFilter();

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('total_amount', __('Total amount'));
        $show->field('course_id', __('course Id'));
        $show->field('status', __('Status'));
        $show->field('created_at', __('Created At'));
        $show->field('updated_at', __('Updated At'));

        return $show;
    }
}
