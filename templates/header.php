<?php




?>

<head>
  <title>Danh bạ điện tử</title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="assets/css/app.css">
  <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
        <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
       
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.2.1/build/ol.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.2.1/css/ol.css" type="text/css">
  <link rel="shortcut icon" href="assets/images/logo/logo.png">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-light" onload="initialize_map();">
  <!--/.Navbar -->
  <!--Navbar -->
  <div class="header">
    <nav class="navbar navbar-expand-lg navbar-light " style="background-color: #e3f2fd;">
      <div class="container">
        <a class="navbar-brand" href="index.php">
          <img src="assets/images/logo/logo.png" height="30px" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Contact</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">About</a>
            </li>
                <li class="nav-item">
                  <a class="nav-link" href="admin.php">Admin</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="manager.php">Manager Users</a>
                </li>
                <li class="nav-link">
                
                </li>
          </ul>
          <form class="d-flex" style="margin-right:15px;">
                <input id="search-location" class="form-control me-2"  placeholder="Search" aria-label="Search">
                <a href="#" class="btn btn-outline-success btn-search">Search</a>
          </form>

          <?php if (isset($_SESSION['email'])) : ?>
            <div class="auth ">
              <div class="btn-group">
                <div class="dropdown-toggle d-flex" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer">
                  <img src="<?php echo $_SESSION['user_image']; ?>" id="avatar" class="avt img-thumbnail m-auto" alt="...">
                  <p class="my-auto mx-2"><b><?php echo $_SESSION['name']; ?></b></p>
                </div>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="user.php?id=<?php echo $_SESSION['id']; ?>">Chỉnh sửa thông tin</a>
                  </li>


                  <!-- Modal -->

                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                </ul>

              </div>
            </div>
          <?php else : ?>
            <div class="auth">
              <a class="btn btn-primary m-1" href="login.php">Đăng nhập</a>
              <a class="btn btn-primary" href="register.php">Đăng ký</a>
            </div>
          <?php endif ?>


        </div>
      </div>
    </nav>
  </div>




  </div>
  <!-- A grey horizontal navbar that becomes vertical on small screens -->