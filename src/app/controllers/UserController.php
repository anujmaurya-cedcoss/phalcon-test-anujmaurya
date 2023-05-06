<?php
use Phalcon\Mvc\Controller;

class UserController extends Controller
{
    public function indexAction()
    {
        // display all users with edit, and delete button
        $user = $this->db->fetchAll(
            "SELECT * FROM `users`",
                \Phalcon\Db\Enum::FETCH_ASSOC
        );
        $res = "";
        foreach ($user as $value) {
            $res .= "<tr>
                <td>$value[id]</td>
                <td>$value[email]</td>
                <td>$value[password]</td>
                <td>$value[role]</td><td>";
            $res .= $this->tag->linkTo(
                ['user/edit' . '?pid=' . $value['id'], 'Edit', 'class' => 'btn btn-success']
            );
            $res .= $this->tag->linkTo(
                ['user/delete' . '?pid=' . $value['id'], 'Delete', 'class' => 'btn btn-danger']
            );
            $res .= "</td></tr>";
        }
        $this->view->message = $res;
    }
    public function deleteAction() {
        $this->db->execute("DELETE FROM `users` WHERE id = $_GET[pid]");
        $this->response->redirect('/user/');
    }

    public function editAction() {
        $id = $_GET['pid'];
        $users = $this->db->fetchAll(
            "SELECT * FROM `users` WHERE `id` = \"$id\"",
                \Phalcon\Db\Enum::FETCH_ASSOC
        );
        $user = $users[0];
        $res = "";
        $res = "<form method = 'POST' action = '/user/update?pid=$user[id]'>";
        $res .= "<label for=\"fname\">ID:</label>
        <input type=\"text\" disabled  name=\"id\" value = $user[id]><br><br>";

        $res .= "<label for=\"fname\">E-mail:</label>
        <input type=\"text\"  name=\"email\" value = $user[email]><br><br>";

        $res .= "<label for=\"fname\">Password:</label>
        <input type=\"text\"  name=\"password\" value = $user[password]><br><br>";
        
        $res .= "<label for=\"role\">Assign Role :</label>
        <select name = 'role' id = 'role'>
            <option name = $user[role] selected>$user[role]</option>
            <option name = 'user'>User</option>
            <option name = 'admin'>Admin</option>
            <option name = 'manager'>Manager</option>
            <option name = 'accountant'>Accountant</option>
        </select>
        ";
        
        $res .= "<input type = 'submit' value = 'Update'></textarea>";
        $res .= "</form>";
        
        $this->view->message = $res;
    }
    public function updateAction() {
        $pid = $_GET['pid'];
        $sql = "UPDATE `users` SET `email` = \"$_POST[email]\",
        `password` = \"$_POST[password]\",`role` = \"$_POST[role]\" WHERE id = $pid";
        $this->db->execute($sql);
        $this->response->redirect('/user/');
    }
}
