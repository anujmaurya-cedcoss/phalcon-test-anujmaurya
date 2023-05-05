<?php include_once(APP_PATH.'/views/header.php'); ?>
<?php
session_start();
$class = 'btn btn-info m-3';

// add new product
echo $this->tag->linkTo(
    ['product/add', $this->locale->_('Add new product!'), 'class' => $class]
);
// show all products
echo $this->tag->linkTo(
    ['product/', $this->locale->_('Show all products!'), 'class' => $class]
);
// place new order
echo $this->tag->linkTo(
    ['order', $this->locale->_('Place new Order!'), 'class' => $class]
);
// show all orders
echo $this->tag->linkTo(
    ['order/', $this->locale->_('Show all Order!'), 'class' => $class]
);
// settings
echo $this->tag->linkTo(
    ['setting', $this->locale->_('Setting'), 'class' => $class]
);

// logout
echo $this->tag->linkTo(
    ['signup/logout', $this->locale->_('LogOut'), 'class' => $class]
);
