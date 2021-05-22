<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-compatible" content="ie=edge">
	<title>mystoremanager | Gabrielonso</title>
	<link rel="icon" type="image/png" href="images/shopping_cart_32x32.png" sizes="32x32"></link>
	<link rel="icon" type="image/png" href="images/shopping_cart_16x16.png" sizes="16x16"></link>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css">
	
</head>
<body>		
	<div class="container-fluid" style="background-color: WhiteSmoke;">
			<nav class="nav navbar top-navbar">
				<div class="btn nav-item sidenav-trigger" data-toggle="collapse" data-target="#sidenav">
					<i class="fas fa-bars"></i>
				</div>
				<div class="nav-menu">
					<div class="navbar-brand nav-item">
						<marquee>GABRIELONSO...<i class="note">Here to help...</i></marquee>
					</div>
					<div class="nav-item navbar-right-menu">
						<ul class="navbar-nav">
							<li class="dropdown">
						     	<a href="#" id="navbardrop" class="nav-link dropdown-toggle" data-toggle="dropdown">
						        	<i class="fa fa-user"></i>		
						    	</a>
						    	<div class="dropdown-menu dropdown-menu-right">
						       		<a href="profile.php" class="dropdown-item">Edit Profile</a>
						        	<a href="logout.php" class="dropdown-item">Log Out</a>
						      	</div>
						    </li>
						</ul>
					</div>
				</div>			
			</nav>
			<nav class="sidenav nav collapse" id="sidenav">
					<div class="list-group list-group-flush">
						<li class="nav-item list-group-item user text-center" style="color: #e0f2f1">
							<p class="small">Welcome</p>
							<p class="p-0 mb-0"><i class="fa fa-user"></i></p>
							<p class="mt-0 pt-0">
								<?php echo $_SESSION['user_name'] ?>
							</p>
						</li>
						<a href="index.php" class="nav-item list-group-item list-group-item-action">
							<span class ="icon"><i class='fas fa-tachometer-alt'></i></span>
							<span class="title">Dashboard</span>
						</a>
						<a href="users.php" class="nav-item list-group-item list-group-item-action">
							<span class = "icon"><i class="fa fa-users"></i></span>
							<span class="title">Users</span>
						</a>
						<a href="#goods_itm_collapse" data-toggle="collapse" class="nav-item list-group-item list-group-item-action">
							<span class = "icon"><i class="fa fa-cubes"></i></span>
							<span class="title">Goods Item  <i class="fa fa-caret-down"></i></span>
						</a>
						<div id="goods_itm_collapse" class="collapse list-group list-group-flush">
							<a class="list-group-item  list-group-item-action" href="create_new_item.php">Create new</a>
							<a class="list-group-item  list-group-item-action" href="goods_item.php">View all items</a>
						</div>
						<a href="purchase.php" class="nav-item list-group-item list-group-item-action">
							<span class = "icon"><i class='fas fa-cart-arrow-down'></i></span>
							<span class="title">Purchase</span>
						</a>
						<a href="#sales-collapse" data-toggle="collapse" class="nav-item list-group-item list-group-item-action">
							<span class = "icon"><i class="fa fa-shopping-cart"></i></span>
							<span class="title">Sales  <i class="fa fa-caret-down"></i></span>
						</a>
						<div id="sales-collapse" class="collapse list-group list-group-flush">
							<a class="list-group-item  list-group-item-action" href="sales_invoice.php">Create new invoice</a>
							<a class="list-group-item  list-group-item-action" href="sales.php">View all invoice</a>
							<a class="list-group-item  list-group-item-action" href="customers_orders.php">View customers' orders</a>
						</div>
						<a href="#inventory-collapse" class="nav-item list-group-item list-group-item-action" data-toggle="collapse">
							<span class = "icon"><i class='fas fa-warehouse'></i></span>
							<span class="title">Stocks <i class="fa fa-caret-down"></i></span>
						</a>
						<div class="collapse list-group list-group-flush" id="inventory-collapse">
							<a href="wh_goods_store.php" class="list-group-item  list-group-item-action">Warehouse category (in cartons)</a>
							<a href="rtl_goods_store.php" class="list-group-item  list-group-item-action">Retail category (in units/pcs)
							</a>
						</div>
						<a href="payments.php" class="nav-item list-group-item list-group-item-action">
							<span class = "icon"><i class="far fa-money-bill-alt"></i></span>
							<span class="title">Payments</span>
						</a>
						<a href="logout.php" class="nav-item list-group-item list-group-item-action">
							<span class ="icon"><span class="glyphicon glyphicon-log-out"></span></span>
							<span class="title">Log out</span>
						</a>					
					</div>						
			</nav>
			<div class="modal fade" id="alert-modal" role="dialog" data-backdrop="static" data-keyboard="false">
    			<div class="modal-dialog modal-sm">
      				<div class="modal-content">
       					<div class="modal-header">
         	 				<h4 class="modal-title">Alert!</h4>
          					<button type="button" class="close" data-dismiss="modal">&times;</button>
       	 				</div>
        				<div class="modal-body"></div>    
      				</div>
   				 </div>
  			</div>
  			<div class="modal fade" id="loading-modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
  				<div class="modal-dialog modal-sm">
  					<div class="modal-content" style="width: 48px">
        				<div class="modal-body">
        					<span class="fa fa-spinner fa-spin fa-3x"></span>
        				</div>    
      				</div>
  				</div>
  			</div>
			<div class="main p-3">
