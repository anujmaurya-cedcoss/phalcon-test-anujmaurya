<?php
use Phalcon\Mvc\Controller;

class OrderController extends Controller
{
    public function indexAction()
    {
        // show the list of all orders
        $order = $this->db->fetchAll("SELECT * FROM `orders`", \Phalcon\Db\Enum::FETCH_ASSOC);
        $res = "";
        foreach ($order as $value) {
            $product = $this->db->fetchAll(
                "SELECT * FROM `products` where `prod_id` = '$value[pid]'",
                \Phalcon\Db\Enum::FETCH_ASSOC
            );

            $grandTotal = $product[0]['price'] * $value['quantity'];
            $image = $product[0]['image'];
            $title = $product[0]['title'];
            $description = $product[0]['description'];
            $price = $product[0]['price'];
            $id = $product[0]['id'];
            echo $id;
            $res .= "<div class=\"card text-black\">
            <i class=\"fab fa-apple fa-lg pt-3 pb-1 px-3\"></i>
            <img src=\"$image\" class=\"col-3 card-img-top\" alt=\"$title\" />
            <div class=\"card-body\">
            <div class=\"text-center\">
            <p class=\"text-muted mb-4\">$description</p>
            </div>
            <div>
            <div class=\"d-flex justify-content-between\">
            <span>Price : </span><span>$price</span>
            </div>
            <div class=\"d-flex justify-content-between\">
            <span>Quantity : </span><span>$value[quantity]</span>
            </div>
            </div>
            <div class=\"d-flex justify-content-between total font-weight-bold mt-4\">
                <span>Total</span><span>$$grandTotal</span>
                </div>
                </div>
                </div>";
            $res .= $this->tag->linkTo(
                ['order/edit?oid=' . $value['oid'], 'Edit Order', 'class' => 'btn btn-success m-3']
            );
            $res .= $this->tag->linkTo(
                ['order/delete?oid=' . $value['oid'], 'Delete Order', 'class' => 'btn btn-danger']
            );
        }
        echo $this->view->message = $res;
    }


    public function deleteAction()
    {
        $this->db->execute("DELETE FROM `orders` WHERE `oid` = $_GET[oid]");
        $this->response->redirect('/order/');
    }
    public function editAction()
    {
        $oid = $_GET['oid'];
        $orders = $this->db->fetchAll(
            "SELECT * FROM `orders` where `oid` = '$oid'",
            \Phalcon\Db\Enum::FETCH_ASSOC
        );
        $order = $orders[0];

        $res = "";
        $res = "<form method = 'POST' action = '/order/update?oid=$oid'>";
        $res .= "<label for=\"fname\">Order Id:</label>
        <input type=\"text\" disabled  name=\"prod_id\" value = $order[oid]><br><br>";
        $res .= "<label for=\"fname\">Product Id:</label>
        <input type=\"text\" disabled  name=\"prod_id\" value = $order[pid]><br><br>";
        $res .= "<label for=\"fname\">User Id:</label>
        <input type=\"text\" disabled  name=\"user_id\" value = $order[uid]><br><br>";
        $res .= "<label for=\"status\">Set Status:</label>

        <select name=\"status\" id=\"status\">
          <option value=\"placed\">Placed</option>
          <option value=\"in-process\">In-Process</option>
          <option value=\"in-transit\">In-Transit</option>
          <option value=\"delivered\">Delivered</option>
        </select>";
        $res .= "<input class = 'btn btn-primary m-3' type = 'submit' value = 'Update Order'></input>";
        $res .= "</form>";
        $this->view->message = $res;
    }

    public function updateAction() {
        $oid = $_GET['oid'];
        $sql = "UPDATE `orders` SET `order_status` = \"$_POST[status]\"
        WHERE `oid` = $oid";
        $this->db->execute($sql);
        $this->response->redirect('/order/');
    }
}
