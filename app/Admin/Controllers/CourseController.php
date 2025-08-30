<?php


namespace App\Admin\Controllers;

use App\Models\Course;
use App\Models\CourseType;
use App\Models\User;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class CourseController extends AdminController
{

    protected $title = 'Courses';

    protected function grid()
    {
        $grid = new Grid(new Course());

        $grid->column('id', __('Id'));
        $grid->column('user_token', __('Teacher'))->display(function ($token) {
            return  User::where('token', '=', $token)->value('name');
        });
        $grid->column('name', __('Name'));
        $grid->column('thumbnail', __('Thumbnail'))->image('', 50, 50);
        //$grid->column('video', __('Video'));
        $grid->column('description', __('Description'));
        $grid->column('type_id', __('Type Id'));
        $grid->column('price', __('Price'));
        $grid->column('price', __('Price'));
        $grid->column('lesson_num', __('Lesson Num'));
        $grid->column('video_length', __('Vido Length'));
        $grid->column('downloadable_res', __('Donwloadable num'));
        // $grid->column('follow', __('Follow'));
        // $grid->column('score', __('Score'));
        $grid->column('created_at', __('Created at'));
        // $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Course::findOrFail($id));

        $show->field('id', __('Id'));
        //$show->field('user_token', __('Useer Token'));
        $show->field('name', __('Name'));
        $show->field('thumbnail', __('Thumbnail'));
        //$show->field('video', __('Video'));
        $show->field('description', __('Description'));
        //$show->field('type_id', __('Type Id'));
        $show->field('price', __('Price'));
        $show->field('price', __('Price'));
        $show->field('lesson_num', __('Lesson Num'));
        $show->field('video_length', __('Vido Length'));
        $show->field('follow', __('Follow'));
        $show->field('score', __('Score'));
        $show->field('downloadable_res', __('Downloadable Num'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        // $show->disableActions();
        // $show->disableCreateButton();


        return $show;
    }

    protected function form()
    {
        $form = new Form(new Course());
        $form->text('name', __('Name'));

        $result = CourseType::pluck('title', 'id');
        $form->select('type_id', __('Category'))->options($result);
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->file('video', __('Video'))->uniqueName();
        $form->text('description', __('Description'));
        $form->decimal('price', __('Price'));
        $form->number('lesson_num', __('Lesson Number'));
        $form->number('video_length', __('Video Length'));
        $form->number('downloadable_res', __('Downloadable num'));

        $result = User::pluck('name', 'token');
        $form->select('user_token', __('Teacher'))->options($result);
        $form->display('created_at', __('Created at'));
        $form->display('updated_at', __('Updated at'));

        // $form->text('title', __('Title'));
        // $form->textarea('description', __('Description'));
        // $form->number('order', __('Order'));
        return $form;
    }
}
