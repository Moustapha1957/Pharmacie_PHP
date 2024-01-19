<!-- modify_password.php -->
<?php

session_start();

if(!isset($_SESSION['user_session'])){  //User_session

  header("location:index.php");
 
}                       

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css">
    <link rel="stylesheet" href="css/jquery.css">
  <link rel="stylesheet" type="text/css" href="src/facebox.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <style type="text/css">

  </style>
    
    <script src="js/jquery-1.7.2.min.js"></script>
    <script src="js/jquery_ui.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="src/facebox.js"></script>

 
    <script type="text/javascript">
       jQuery(document).ready(function($) {
    $("a[id*=popup]").facebox({
      loadingImage : 'src/img/loading.gif',
      closeImage   : 'src/img/closelabel.png'
    })
  })
    </script>

<script type="text/javascript">


//GET Medicine Name And Expire Date

  $(document).ready(function(){

       $("#qty").focus(

            function(){

              var medicine_name = $("#product_hidden").val();
              var expire_date   = $("#date_hidden").val();

            $.ajax({
              type:'POST',
              url :'auto.php',
              dataType:"json",
              data:{medicine_name:medicine_name,expire_date:expire_date},
              success:function(data){

                $("#avai_qty").val(data);
              },

            });
    });

//GET Medicine Name And Expire Date

         //Disabled Button If Quantity Not Available

          $("#qty").blur(function(){

             var avai_qty = $("#avai_qty").val();
             var in_qty = parseInt($("#qty").val());
             var avai_qty_int = parseInt($("#avai_qty").val());
             if(avai_qty == "" ||  in_qty > avai_qty_int || in_qty <= 0){
                    
                    $("#btn_submit").attr('disabled','disabled');
                    alert("Quelque chose s'est mal passé !!");

             }
             else{

              $("#btn_submit").removeAttr('disabled');

             }

          });

         //Disabled Button If Quantity Not Available
});
     </script>

     <script language="javascript" type="text/javascript">

      var timerID = null;
  var timerRunning = false;

  function stopclock() {
    if (timerRunning)
      clearTimeout(timerID);
    timerRunning = false;
  }

  function showtime() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    var timeValue = ("0" + hours).slice(-2) + ":" + ("0" + minutes).slice(-2) + ":" + ("0" + seconds).slice(-2);

    document.getElementById("clock").innerHTML = timeValue;

    timerID = setTimeout("showtime()", 1000);
    timerRunning = true;
  }

  function startclock() {
    stopclock();
    showtime();
  }

  window.onload = startclock;

   //Clock
       
     </script>
</head>
<body>
 <div class="navbar navbar-inverse navbar-fixed-top"><!--*****Header******-->

      <div class=" navbar-inner">
        <div class="container-fluid">

          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
          </a>

          <a class="brand" href="#"><b>Pharmacie</b></a>
           <div class="nav-collapse">

            <ul class="nav pull-right">
               


               <li>

               
        <?php 
        include("dbcon.php");

          $quantity = "10";
          $select_sql1 = "SELECT * FROM stock where remain_quantity <= '$quantity' and status='Available'";
          $result1 = mysqli_query($con,$select_sql1);
          $row2 = $result1->num_rows;

         if($row2 == 0){

            echo ' <a  href="#" class="notification label-inverse" >
                <span class="icon-exclamation-sign icon-large"></span></a>';

          }else{
            echo ' <a  href="qty_alert.php" class="notification label-inverse" id="popup">
                <span class="icon-exclamation-sign icon-large"></span>
                <span class="badge">'.$row2.'</span></a>';

    
          }


          ?> 
        </li>
          <li>
            <?php
              $date = date('d-m-Y');    
        $inc_date = date("Y-m-d", strtotime("+6 month", strtotime($date))); 
        $select_sql = "SELECT  * FROM stock WHERE expire_date <= '$inc_date' and status='Available' ";
         $result =  mysqli_query($con,$select_sql); 
          $row1 = $result->num_rows;

            if($row1 == 0){

                 echo ' <a  href="#" class="notification label-inverse" >
                <span class="icon-bell icon-large"></span></a>';

          }else{
            echo ' <a  href="ex_alert.php" class="notification label-inverse" id="popup">
                <span class="icon-bell icon-large"></span>
                <span class="badge">'.$row1.'</span></a>';

            }
            ?>
            
          </li>
         <li><a href="product/view.php?invoice_number=<?php echo $_GET['invoice_number']?>"><span class="icon-th"></span> Produits</a></li>
          <li><a href="sales_report.php?invoice_number=<?php echo $_GET['invoice_number']?>"><span class="icon-bar-chart"></span> Rapport des ventes</a></li>   
         <li><a href="backup.php?invoice_number=<?php echo $_GET['invoice_number']?>"><span class="icon-folder-open"></span> Sauvegarde</a></li>
         <li><a href="modify_password.php?invoice_number=<?php echo $_GET['invoice_number']?>"><span class="icon-folder-open"></span> Modifier le Password</a></li>
         <li><a href="logout.php" class="link"><font color='red'><span class="icon-off"></span></font> Se déconnecter</a></li>
       </ul>
         </div>
        </div>
      </div>
  </div><!--*****Header******-->

  <div class="container"><!-- Conteneur principal pour le centrage du contenu -->
    <div class="content">

<?php



if (isset($_SESSION['user_id'])) {
    if (isset($_POST['update_password'])) {
        $new_password = $_POST['new_password'];

        // Assurez-vous de sécuriser le mot de passe (par exemple, en utilisant des fonctions de hachage)
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Utiliser des requêtes préparées pour éviter les problèmes de syntaxe SQL et améliorer la sécurité
        $update_sql = "UPDATE users SET password=? WHERE id=?";
        $stmt = mysqli_prepare($con, $update_sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $_SESSION['user_id']);
            if (mysqli_stmt_execute($stmt)) {


                echo "Mot de passe mis à jour avec succès!";
            } else {
                echo "Erreur lors de la mise à jour du mot de passe: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Erreur de préparation de la requête: " . mysqli_error($con);
        }
    } else {
        echo '
            <form method="POST" class="center-form">
                <label for="new_password">Nouveau mot de passe:</label>
                <input type="password" name="new_password" required>
                <br>
                <input type="submit" name="update_password" value="Modifier le mot de passe">
            </form>
        ';
    }
} else {
    echo "Erreur: user_id non défini dans la session.";
}

?>

</div>
  </div>

</body>
</html>
