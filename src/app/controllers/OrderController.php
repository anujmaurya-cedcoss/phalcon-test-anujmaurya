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
                </div>

                <form method=\"POST\" action='?pid=$id'>
    Set Status
    <select name=\"setstatus\" onchange=\"this.form.submit()\">
        <option value=\"placed\">Placed</option>
        <option value=\"in-process\">In-process</option>
        <option value=\"in-transit\">In-transit</option>
        <option value=\"delivered\">Delivered</option>
    </select>
</form>";
        }
        if (isset($_POST["setstatus"])) {
            echo $_GET['pid'];
            $sql = "UPDATE `orders` SET `order_status`=\"$_POST[setstatus]\" WHERE `pid` = $value[pid]";
            $this->db->execute($sql);
        }
        echo $this->view->message = $res;
    }
}