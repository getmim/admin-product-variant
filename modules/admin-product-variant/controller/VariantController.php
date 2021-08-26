<?php
/**
 * VariantController
 * @package admin-product-variant
 * @version 0.0.1
 */

namespace AdminProductVariant\Controller;

use Product\Model\Product;
use LibFormatter\Library\Formatter;
use ProductVariant\Model\ProductVariant as PVariant;
use LibForm\Library\Form;

class VariantController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['product', 'all-product']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    private function getProduct(string $param): ?object
    {
        $cond = [
            'status' => ['__op', '>', 0],
            'id' => $this->req->param->$param
        ];

        if(!$this->can_i->manage_product_all)
            $cond['user'] = $this->user->id;

        $product = Product::getOne($cond);
        if (!$product)
            return null;

        return Formatter::format('product', $product);
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_product && !$this->can_i->manage_product_all)
            return $this->show404();

        $product = $this->getProduct('product');
        if(!$product)
            return $this->show404();

        $variant = (object)[];

        $id = $this->req->param->id;
        if($id){
            $cond = [
                'id' => $id,
                'status' => 1
            ];
            $variant = PVariant::getOne(['id'=>$id]);
            if(!$variant)
                return $this->show404();
            $params = $this->getParams('Edit Product Variant');
        }else{
            $params = $this->getParams('Create New Product Variant');
        }

        $form           = new Form('admin.product-variant.edit');
        $params['form'] = $form;
        $params['product'] = $product;

        if(!($valid = $form->validate($variant)) || !$form->csrfTest('noob'))
            return $this->resp('product/variant/edit', $params);

        if($id){
            if(!PVariant::set((array)$valid, ['id'=>$id]))
                deb(PVariant::lastError());
        }else{
            $valid->product = $product->id->value;
            $valid->user = $this->user->id;
            if(!($id = PVariant::create((array)$valid)))
                deb(PVariant::lastError());
        }

        // add the log
        $this->addLog([
            'user'     => $this->user->id,
            'object'   => $id,
            'parent'   => $product->id->value,
            'method'   => isset($variant->id) ? 2 : 1,
            'type'     => 'product-variant',
            'original' => $variant,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminProductVariant', ['product' => $product->id]);
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_product && !$this->can_i->manage_product_all)
            return $this->show404();

        $product = $this->getProduct('product');
        if(!$product)
            return $this->show404();

        $cond = [
            'product' => $product->id->value,
            'status' => ['__op', '>', 0]
        ];

        $variants = PVariant::get($cond, 0, 1, ['name'=>true]) ?? [];
        if($variants)
            $variants = Formatter::formatMany('product-variant', $variants, ['user']);

        $params             = $this->getParams('Product');
        $params['variants'] = $variants;
        $params['product']  = $product;
        $params['total']    = PVariant::count($cond);

        $this->resp('product/variant/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->remove_product)
            return $this->show404();

        $product = $this->getProduct('product');
        if(!$product)
            return $this->show404();

        $id = $this->req->param->id;

        $cond = [
            'id' => $this->req->param->id
        ];

        $variant = PVariant::getOne($cond);
        $next    = $this->router->to('adminProductVariant', ['product' => $product->id]);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => $product->id->value,
            'method' => 3,
            'type'   => 'product-variant',
            'original' => $variant,
            'changes'  => null
        ]);

        $variant_set = [
            'status' => 0
        ];
        PVariant::set($variant_set, ['id'=>$id]);

        $this->res->redirect($next);
    }
}
