<!-- Show these admin pages only when the admin is logged in -->
<?php  require '../assets/partials/_admin-check.php';   ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesan</title>
        <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/d8cfbe84b9.js" crossorigin="anonymous"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <!-- CSS -->
    <?php 
        require '../assets/styles/admin.php';
        require '../assets/styles/admin-options.php';
        $page="customer";
    ?>
</head>
<body>
    <!-- Requiring the admin header files -->
    <?php require '../assets/partials/_admin-header.php';?>

    <!-- Add, Edit and Delete Customers -->
    <?php
        /*
            1. Check if an admin is logged in
            2. Check if the request method is POST
        */
        if($loggedIn && $_SERVER["REQUEST_METHOD"] == "POST")
        {
            if(isset($_POST["submit"]))
            {
                /*
                    ADDING Customers
                 Check if the $_POST key 'submit' exists
                */
                // Should be validated client-side
                $cname = $_POST["cfirstname"] . " " . $_POST["clastname"];
                $cphone = $_POST["cphone"];
        
                $customer_exists = exist_customers($conn,$cname,$cphone);
                $customer_added = false;
        
                if(!$customer_exists)
                {
                    // Route is unique, proceed
                    $sql = "INSERT INTO `customers` (`customer_name`, `customer_phone`, `customer_created`) VALUES ('$cname', '$cphone', current_timestamp());";
                    $result = mysqli_query($conn, $sql);
                    // Gives back the Auto Increment id
                    $autoInc_id = mysqli_insert_id($conn);
                    // If the id exists then, 
                    if($autoInc_id)
                    {
                        $code = rand(1,99999);
                        // Generates the unique userid
                        $customer_id = "CUST-".$code.$autoInc_id;
                        
                        $query = "UPDATE `customers` SET `customer_id` = '$customer_id' WHERE `customers`.`id` = $autoInc_id;";
                        $queryResult = mysqli_query($conn, $query);

                        if(!$queryResult)
                            echo "Not Working";
                    }

                    if($result)
                        $customer_added = true;
                }
    
                if($customer_added)
                {
                    // Show success alert
                    echo '<div class="my-0 alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Successful!</strong> pemesan ditambahkan
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
                else{
                    // Show error alert
                    echo '<div class="my-0 alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Data pemesan sudah ada
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
            }
            if(isset($_POST["edit"]))
            {
                // EDIT ROUTES
                $cname = $_POST["cname"];
                $cphone = $_POST["cphone"];
                $id = $_POST["id"];
                $id_if_customer_exists = exist_customers($conn,$cname,$cphone);

                if(!$id_if_customer_exists || $id == $id_if_customer_exists)
                {
                    $updateSql = "UPDATE `customers` SET
                    `customer_name` = '$cname',
                    `customer_phone` = '$cphone' WHERE `customers`.`customer_id` = '$id';";

                    $updateResult = mysqli_query($conn, $updateSql);
                    $rowsAffected = mysqli_affected_rows($conn);
    
                    $messageStatus = "danger";
                    $messageInfo = "";
                    $messageHeading = "Error!";
    
                    if(!$rowsAffected)
                    {
                        $messageInfo = "No Edits Administered!";
                    }
    
                    elseif($updateResult)
                    {
                        // Show success alert
                        $messageStatus = "success";
                        $messageHeading = "Successfull!";
                        $messageInfo = "Detail pemesan diubah";
                    }
                    else{
                        // Show error alert
                        $messageInfo = "Permintaan Anda tidak dapat diproses karena masalah teknis dari pihak kami. Kami mohon maaf atas ketidaknyamanan yang ditimbulkan";
                    }
    
                    // MESSAGE
                    echo '<div class="my-0 alert alert-'.$messageStatus.' alert-dismissible fade show" role="alert">
                    <strong>'.$messageHeading.'</strong> '.$messageInfo.'
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
                else{
                    // If customer details already exists
                    echo '<div class="my-0 alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> pelanggan sudah ada
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }

            }
            if(isset($_POST["delete"]))
            {
                // DELETE ROUTES
                $id = $_POST["id"];
                // Delete the route with id => id
                $deleteSql = "DELETE FROM `customers` WHERE `customers`.`id` = $id";

                $deleteResult = mysqli_query($conn, $deleteSql);
                $rowsAffected = mysqli_affected_rows($conn);
                $messageStatus = "danger";
                $messageInfo = "";
                $messageHeading = "Error!";

                if(!$rowsAffected)
                {
                    $messageInfo = "Record Doesnt Exist";
                }

                elseif($deleteResult)
                {   
                    $messageStatus = "success";
                    $messageInfo = "detail pemesan deleted";
                    $messageHeading = "Successfull!";
                }
                else{

                    $messageInfo = "Permintaan Anda tidak dapat diproses karena masalah teknis dari pihak kami. Kami mohon maaf atas ketidaknyamanan yang ditimbulkan";
                }

                // Message
                echo '<div class="my-0 alert alert-'.$messageStatus.' alert-dismissible fade show" role="alert">
                <strong>'.$messageHeading.'</strong> '.$messageInfo.'
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        }
        ?>
        <?php
            $resultSql = "SELECT * FROM `customers` ORDER BY customer_created DESC";
                            
            $resultSqlResult = mysqli_query($conn, $resultSql);

            if(!mysqli_num_rows($resultSqlResult)){ ?>
                <!-- Customers are not present -->
                <div class="container mt-4">
                    <div id="noCustomers" class="alert alert-dark " role="alert">
                        <h1 class="alert-heading">Pemesan Tidak Ditemukan</h1>
                        <p class="fw-light">Jadi pemesan yang pertama!</p>
                        <hr>
                        <div id="addCustomerAlert" class="alert alert-success" role="alert">
                                Click on <button id="add-button" class="button btn-sm"type="button"data-bs-toggle="modal" data-bs-target="#addModal">Tambah <i class="fas fa-plus"></i></button> untuk menambahkan pemesan!
                        </div>
                    </div>
                </div>
            <?php }
            else { ?>   
            <!-- If Customers are present -->
            <section id="customer">
                <div id="head">
                    <h4>Status pemesan</h4>
                </div>
                <div id="customer-results">
                    <div>
                        <button id="add-button" class="button btn-sm"type="button"data-bs-toggle="modal" data-bs-target="#addModal">Tambah detail pemesan <i class="fas fa-plus"></i></button>
                    </div>
                    <table class="table table-hover table-bordered">
                        <thead>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Kontak</th>
                            <th>Actions</th>
                        </thead>
                        <?php
                            while($row = mysqli_fetch_assoc($resultSqlResult))
                            {
                                    // echo "<pre>";
                                    // var_export($row);
                                    // echo "</pre>";
                                $id = $row["id"];
                                $customer_id = $row["customer_id"];
                                $customer_name = $row["customer_name"];
                                $customer_phone = $row["customer_phone"]; 
                        ?>
                        <tr>
                            <td>
                                <?php
                                    echo $customer_id;
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo $customer_name;
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo $customer_phone;
                                ?>
                            </td>
                            <td>
                            <button class="button edit-button " data-link="<?php echo $_SERVER['REQUEST_URI']; ?>" data-id="<?php 
                                                echo $customer_id;?>" data-name="<?php 
                                                echo $customer_name;?>" data-phone="<?php 
                                                echo $customer_phone;?>"
                                                >Edit</button>
                                            <button class="button delete-button" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php 
                                                echo $id;?>">Delete</button>
                            </td>
                        </tr>
                    <?php 
                        }
                    ?>
                    </table>
                </div>
            </section>
            <?php } ?>   
        </div>
    </main>
    <!-- All Modals Here -->
    <!-- Add Route Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Pemesan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addCustomerForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                            <div class="mb-3">
                                <label for="cfirstname" class="form-label">Nama depan pemesan</label>
                                <input type="text" class="form-control" id="cfirstname" name="cfirstname">
                            </div>
                            <div class="mb-3">
                                <label for="clastname" class="form-label">Nama Belakang pemesan</label>
                                <input type="text" class="form-control" id="clastname" name="clastname">
                            </div>
                            <div class="mb-3">
                                <label for="cphone" class="form-label">No Hp</label>
                                <input type="tel" class="form-control" id="cphone" name="cphone">
                            </div>
                            <button type="submit" class="btn btn-success" name="submit">Submit</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <!-- Add Anything -->
                    </div>
                    </div>
                </div>
        </div>
        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-exclamation-circle"></i></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h2 class="text-center pb-4">
                    Anda Yakin?
                </h2>
                <p>
                Apakah Anda benar-benar ingin menghapus detail pelanggan ini? <strong>proses ini tidak dapat dipulihkan.</strong>
                </p>
                <!-- Needed to pass id -->
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="delete-form"  method="POST">
                    <input id="delete-id" type="hidden" name="id">
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="delete-form" name="delete" class="btn btn-danger">Delete</button>
            </div>
            </div>
        </div>
    </div>
    <!-- External JS -->
    <script src="../assets/scripts/admin_customer.js"></script>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
</body>
</html>