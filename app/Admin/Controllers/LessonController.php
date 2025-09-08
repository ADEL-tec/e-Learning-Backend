<?php

namespace App\Admin\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class LessonController extends AdminController
{

    protected $title = 'Lessons';

    protected function grid()
    {
        $grid = new Grid(new Lesson());

        $grid->column('id', __('ID'));
        $grid->column('name', __('Name'));
        $grid->column('course_id', __('Course id'));
        $grid->column('thumbnail', __('Thumbnail'))->image(50, 50);
        $grid->column('description', __('Description'));
        // $grid->column('video', __('Video'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    protected function detail($id)
    {
        $show = new Show(Lesson::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('name', __('Name'));
        $show->field('course_id', __('course Id'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('description', __('Description'));
        //$show->field('video', __('Video'));
        $show->field('created_at', __('Created At'));
        $show->field('updated_at', __('Updated At'));

        return $show;
    }

    protected function form()
    {
        $form = new Form(new Lesson());

        $form->text('name', __('Name'));
        $result = Course::pluck('name', 'id');
        $form->select('course_id', __('Course'))->options($result);
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->textarea('description', __('Description'));
        if ($form->isEditing()) {
            $form->table('video', function ($form) {
                $form->text('name');
                $form->image('thumbnail')->uniqueName();
                $form->file('url');
            });
        } else {
            $form->table('video', function ($form) {
                $form->text('name')->rules('required');
                $form->image('thumbnail')->uniqueName()->rules('required');
                $form->file('url')->rules('required');
            });
        }

        $form->saving(function (Form $form) {
            if ($form->isEditing()) {

                // from the Form
                $video = $form->video;
                // from database
                $res = $form->model()->video;
                $domainPath = env('APP_URL') . 'uploads/';

                $newVideo = [];
                foreach ($video as $k => $v) {
                    $oldVideo = [];
                    if (empty($v['url'])) {
                        $oldVideo['old_url'] = empty($res[$k]['url'])
                            ? ''
                            : str_replace($domainPath, "", $res[$k]['url']);
                    } else {
                        $oldVideo['url'] = $v['url'];
                    }

                    if (empty($v['thumbnail'])) {
                        $oldVideo['old_thumbnail'] = empty($res[$k]['thumbnail'])
                            ? ''
                            : str_replace($domainPath, "", $res[$k]['thumbnail']);
                    } else {
                        $oldVideo['thumbnail'] = $v['thumbnail'];
                    }

                    if (empty($v['name'])) {
                        $oldVideo['name'] = empty($res[$k]['name'])
                            ? ''
                            : str_replace($domainPath, "", $res[$k]['name']);
                    } else {
                        $oldVideo['name'] = $v['name'];
                    }

                    $oldVideo["_remove_"] = $v['_remove_'];

                    array_push($newVideo, $oldVideo);
                }
                $form->model()->video = $newVideo;
            }
        });

        return $form;
    }
}
