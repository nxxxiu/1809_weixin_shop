<?php

namespace App\Admin\Controllers;

use App\Goods;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GoodsController extends Controller
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
        $grid = new Grid(new Goods);

        $grid->goods_id('Goods id');
        $grid->goods_name('商品名称');
        $grid->goods_price('商品价格');
        $grid->goods_desc('商品描述');
        $grid->goods_img('商品图片')->image();
        $grid->goods_store('商品库存');
        $grid->goods_status('商品状态');
        $grid->add_time('添加时间');

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
        $show = new Show(Goods::findOrFail($id));

        $show->goods_id('Goods id');
        $show->goods_name('Goods name');
        $show->goods_price('Goods price');
        $show->goods_desc('Goods desc');
        $show->goods_store('Goods store');
        $show->goods_status('Goods status');
        $show->add_time('Add time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Goods);

        $form->number('goods_id', '商品ID');
        $form->text('goods_name', '商品名称');
        $form->number('goods_price', '商品价格');
        $form->text('goods_desc', '商品描述');
        $form->number('goods_store', '商品库存');
        $form->switch('goods_status', '商品状态');
        $form->image('goods_img','商品图片');

        return $form;
    }
}
