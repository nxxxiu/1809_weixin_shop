<?php

namespace App\Admin\Controllers;

use App\WxUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WxUser);

        $grid->id('Id');
        $grid->openid('Openid');
        $grid->nickname('Nickname');
        $grid->sex('Sex');
        $grid->city('City');
        $grid->province('Province');
        $grid->country('Country');
        $grid->headimgurl('Headimgurl');
        $grid->subscribe_time('Subscribe time');
        $grid->subscribe_scene('Subscribe scene');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WxUser::findOrFail($id));

        $show->id('Id');
        $show->openid('Openid');
        $show->nickname('Nickname');
        $show->sex('Sex');
        $show->city('City');
        $show->province('Province');
        $show->country('Country');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');
        $show->subscribe_scene('Subscribe scene');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WxUser);

        $form->text('openid', 'Openid');
        $form->text('nickname', 'Nickname');
        $form->switch('sex', 'Sex');
        $form->text('city', 'City');
        $form->text('province', 'Province');
        $form->text('country', 'Country');
        $form->text('headimgurl', 'Headimgurl');
        $form->number('subscribe_time', 'Subscribe time');
        $form->text('subscribe_scene', 'Subscribe scene');

        return $form;
    }
}
