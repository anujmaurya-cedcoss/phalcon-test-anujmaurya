<?php
include_once(APP_PATH.'/views/header.php');
session_start();
$class = 'btn btn-primary m-3';

// add new product
echo $this->tag->linkTo(
    ['product/add', $this->locale->_('Add new product!'), 'class' => $class]
);
// show all products
echo $this->tag->linkTo(
    ['product/', $this->locale->_('Show all products!'), 'class' => $class]
);
// show all orders
echo $this->tag->linkTo(
    ['order/', $this->locale->_('Show all Order!'), 'class' => $class]
);
// settings
echo $this->tag->linkTo(
    ['setting', $this->locale->_('Setting'), 'class' => $class]
);
// edit product
echo $this->tag->linkTo(
    ['product/productcrud', $this->locale->_('Edit Products'), 'class' => $class]
);
// logout
echo $this->tag->linkTo(
    ['signup/logout', $this->locale->_('LogOut'), 'class' => $class]
);
