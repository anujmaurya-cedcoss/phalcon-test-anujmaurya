<?php
use Phalcon\Mvc\Controller;

class ProductController extends Controller
{
    public function indexAction()
    {
        $product = $this->db->fetchAll(
            "SELECT * FROM products",
                \Phalcon\Db\Enum::FETCH_ASSOC
        );
        $res = "";
        foreach ($product as $value) {
            $res .= "<div class=\"card\">
            <div class=\"bg-image hover-zoom ripple ripple-surface ripple-surface-light\"
              data-mdb-ripple-color=\"light\">
              <img src=\"$value[image]\"
                class=\"w-100\" alt ='image' />
              <a href=\"#!\">
                <div class=\"mask\">
                  <div class=\"d-flex justify-content-start align-items-end h-100\">
                    <h5><span class=\"badge bg-primary ms-2\">New</span></h5>
                  </div>
                </div>
                <div class=\"hover-overlay\">
                  <div class=\"mask\" style=\"background-color: rgba(251, 251, 251, 0.15);\"></div>
                </div>
              </a>
            </div>
            <div class=\"card-body\">
              <a href=\"\" class=\"text-reset\">
                <h5 class=\"card-title mb-3\">$value[title]</h5>
              </a>
              <a href=\"\" class=\"text-reset\">
                <p>$value[category]</p>
              </a>
              <h6 class=\"mb-3\">$$value[price]</h6>";
            $res .= $this->tag->linkTo(
                ['product/buy?pid=' . $value['prod_id'], 'Buy Now', 'class' => 'btn btn-success']
            );
            $res .= "</div>
          </div>";
        }
        echo $this->view->message = $res;
    }
    public function buyAction()
    {
        $pid = $_GET['pid'];
        $uid = $_SESSION['user'];
        $this->db->execute(
            "INSERT INTO `orders`(`uid`, `pid`, `quantity`)
            VALUES ($uid, $pid, 1)"
        );
        $this->response->redirect('product/myorders');
    }

    public function myordersAction()
    {
        $order = $this->db->fetchAll("SELECT * FROM `orders` WHERE `uid` = '$_SESSION[user]'", \Phalcon\Db\Enum::FETCH_ASSOC);
        $res = "";
        foreach ($order as $value) {
            $product = $this->db->fetchAll("SELECT * FROM `products` where `prod_id` = '$value[pid]'", \Phalcon\Db\Enum::FETCH_ASSOC);
            $grandTotal = $product[0]['price'] * $value['quantity'];
            $image = $product[0]['image'];
            $title = $product[0]['title'];
            $description = $product[0]['description'];
            $price = $product[0]['price'];
            $res .= "<div class=\"card text-black\">
            <i class=\"fab fa-apple fa-lg pt-3 pb-1 px-3\"></i>
            <img src=\"$image\"
            class=\"card-img-top\" alt=\"$title\" />
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
        }
        echo $this->view->message = $res;
    }

    public function productcrudAction()
    {
        $product = $this->db->fetchAll("SELECT * FROM `products`", \Phalcon\Db\Enum::FETCH_ASSOC);
        $res = "";
        foreach ($product as $value) {
            $res .= "<tr>
                    <td><img src = $value[image] alt = 'img' width=\"300\" height=\"300\"></td>
                    <td>$value[title]</td>
                    <td>$value[description]</td>
                    <td>$value[price]</td>
                    <td>$value[quantity_remaining]</td>";
            $res .= "<td>";
            $res .= $this->tag->linkTo(
                ['product/edit' . '?pid=' . $value['prod_id'], 'Edit', 'class' => 'btn btn-success']
            );
            $res .= $this->tag->linkTo(
                ['product/delete' . '?pid=' . $value['prod_id'], 'Delete', 'class' => 'btn btn-danger']
            );

            $res .= "</td></tr>";
        }
        echo $this->view->message = $res;
    }

    public function deleteAction()
    {
        $this->db->execute("DELETE FROM `products` WHERE prod_id = $_GET[pid]");
        $this->response->redirect('/product/productcrud');
    }

    public function editAction()
    {
        $pid = $_GET['pid'];
        $product = $this->db->fetchAll(
            "SELECT * FROM `products` where `prod_id` = '$pid'", \Phalcon\Db\Enum::FETCH_ASSOC
        );
        $prod = $product[0];
        $res = "<form method = 'POST' action = '/product/update?pid=$prod[prod_id]'>";
        $res .= "<label for=\"fname\">Product Id:</label>
        <input type=\"text\" disabled  name=\"prod_id\" value = $prod[prod_id]><br><br>";

        $res .= "<label for=\"fname\">Title :</label>
        <textarea type=\"text\"   name=\"title\" >$prod[title]</textarea>><br><br>";

        $res .= "<label for=\"fname\">Price:</label>
        <input type=\"number\"   name=\"price\" value = $prod[price]><br><br>";

        $res .= "<label for=\"fname\">Description:</label>
        <textarea id=\"w3review\" rows=\"4\" cols=\"50\" name = 'description'>
        $prod[description]
        </textarea><br>";
        $res .= "<label for=\"fname\">Category:</label>
        <input type=\"text\"   name=\"category\" value = $prod[category]><br><br>";

        $res .= "<label for=\"fname\">Image:</label>
        <input type=\"text\"   name=\"image\" value = $prod[image]><br><br>";

        $res .= "<label for=\"fname\">Rating Count:</label>
        <input type=\"text\"   name=\"rating_count\" value = $prod[rating_count]><br><br>";

        $res .= "<label for=\"fname\">Rating Points:</label>
        <input type=\"text\"   name=\"rating_points\" value = $prod[rating_points]><br><br>";

        $res .= "<label for=\"fname\">Quantity Remaining:</label>
        <input type=\"text\"   name=\"quantity_remaining\" value = $prod[quantity_remaining]><br><br>";
        $res .= "<input type = 'submit' value = 'Update'></textarea>";
        $res .= "</form>";
        $this->view->message = $res;
    }

    public function updateAction()
    {
        $pid = $_GET['pid'];
        $sql = "UPDATE `products` SET `title` = \"$_POST[title]\",`price` = \"$_POST[price]\",
        `description` = \"$_POST[description]\",`category` = \"$_POST[category]\",
        `image` = \"$_POST[image]\",`rating_count` = \"$_POST[rating_count]\",
        `rating_points` = \"$_POST[rating_points]\",`quantity_remaining` = \"$_POST[quantity_remaining]\"
        WHERE prod_id = $pid";
        $this->db->execute($sql);
        $this->response->redirect('/product/productcrud');
    }
    public function addAction() {
        $res = "<form method = 'POST' action = '/product/addnew'>";
        $res .= "<label for=\"fname\">Product Id:</label>
        <input type=\"text\" disabled  name=\"prod_id\" placeholder = 'Product ID'><br><br>";

        $res .= "<label for=\"fname\">Title :</label>
        <textarea type=\"text\"   name=\"title\" ></textarea>><br><br>";

        $res .= "<label for=\"fname\">Price:</label>
        <input type=\"number\"   name=\"price\" placeholder = 'Price'><br><br>";

        $res .= "<label for=\"fname\">Description:</label>
        <textarea id=\"w3review\" rows=\"4\" cols=\"50\" name = 'description'>
        </textarea><br>";
        $res .= "<label for=\"fname\">Category:</label>
        <input type=\"text\"   name=\"category\" placeholder = 'Category'><br><br>";

        $res .= "<label for=\"fname\">Image:</label>
        <input type=\"text\"   name=\"image\" placeholder = 'Image Link'><br><br>";

        $res .= "<label for=\"fname\">Rating Count:</label>
        <input type=\"text\"   name=\"rating_count\" placeholder = 'Rating Count'><br><br>";

        $res .= "<label for=\"fname\">Rating Points:</label>
        <input type=\"text\"   name=\"rating_points\" placeholder = 'Raring Points'><br><br>";

        $res .= "<label for=\"fname\">Quantity Remaining:</label>
        <input type=\"text\"   name=\"quantity_remaining\" placeholder = 'Quantity Remaining'><br><br>";
        $res .= "<input type = 'submit' value = 'Add New Product'></textarea>";
        $res .= "</form>";
        $this->view->message = $res;
    }
    public function addnewAction() {
        $sql = "INSERT INTO `products`(`title`, `price`, `description`, `category`, `image`, `rating_count`, `rating_points`, `quantity_remaining`)
        VALUES ($_POST[title],$_POST[price],$_POST[description],$_POST[category],$_POST[image],$_POST[rating_count],$_POST[rating_points],$_POST[quantity_remaining])";
        $this->db->execute($sql);
        $this->response->redirect('/product/productcrud');
    }
}