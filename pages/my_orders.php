<?php
session_start();
include "../navbar.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />

    <!-- Font Awesome Icons -->

    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" />

    <!-- Our CSS -->

    <link rel="stylesheet" href="../styling/orders.css" />

    <title>My Orders</title>
</head>

<body>
    <div class="container mt-4 mb-4">
        <h3>Your Orders: </h3>

        <?php

        //prepare values for the query1
        $user_id = $_SESSION["userID"];

        //select all orders completed by the logged in user
        $query = "SELECT * FROM order_info WHERE customer_id_fk = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);

        if ($result->num_rows === 0) {
            exit("nothing to show");
        }

        //display all orders in a table format
        else {
            echo "
            <table>
                <thead>
                    <tr>
                    <th id = 'order-id'>ORDER_ID</th>
                    <th id = 'contractor'>CONTRACTOR</th>
                    <th id = 'specialization'>SERVICE</th>
                    <th id = 'total-price'>TOTAL PRICE</th>
                    <th id = 'order-date'>ORDER DATE</th>
                    <th id = 'duration'>DURATION</th>
                    <th id = 'rooms'>ROOMS SELECTED</th></tr>
                <thead>
                <tbody>";

            $order_counter = 0;

            //traverse through all the selected orders and for each
            foreach ($orders as $order) {

                $order_id = $order["order_id"];

                //display order info
                echo "<tr>";
                echo "<td id = 'order-id'>" . $order_id . "</td>";

                //prepare values for the query2
                $company_id = $order["contractor_id_fk"]; //get contractor name from the contractor id

                //select company from db that is connected to the order
                $query2 = "SELECT * FROM contractor WHERE contractor_id = ?";

                $stmt = $conn->prepare($query2);
                $stmt->bind_param("i", $company_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $company = $result->fetch_assoc();

                //display company info
                echo "<td id = 'contractor'>" . $company["company_name"] . "</td>";
                echo "<td id = 'specialization'>" . $company["specialization"], "</td>";
                echo "<td id = 'total-price'>$" . number_format($order["total_price"]) . "</td>";
                echo "<td id = 'order-date'>" . $order["order_date"] . "</td>";
                echo "<td id = 'duration'>" . $order["project_duration"] . " weeks</td>";

                //display service info
                echo "<td id = 'rooms'>";

                //select service from db that is connected to the order
                $query = "SELECT service_id FROM service WHERE order_id_fk = ?";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $service_id = $result->fetch_assoc();

                //select all rooms from db that are connected to the order
                $query3 = "SELECT room_name FROM room WHERE service_id_fk = ?";

                $stmt3 = $conn->prepare($query3);
                $stmt3->bind_param("i", $service_id["service_id"]);
                $stmt3->execute();
                $result3 = $stmt3->get_result();
                $rooms = $result3->fetch_all(MYSQLI_ASSOC);

                //display a list of rooms connected to the order
                if ($result3->num_rows === 0) {
                    echo "nothing to show";
                } else {
                    $index = 0; //counter needed to find the last element for different formatting
                    foreach ($rooms as $room) {
                        echo $room["room_name"];
                        if ($index !== count($rooms) - 1) {
                            echo ", ";
                        }
                        $index++;
                    }
                }

                echo "</td></tr></tbody>";
            }
            echo "</table>";
        }

        ?>
    </div>
</body>

</html>