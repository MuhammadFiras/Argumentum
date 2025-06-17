<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title><?= esc($title); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link href="/assets/css/admin-styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="<?= site_url('/admin/dashboard'); ?>">Halaman Admin</a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto me-3 me-lg-4">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
          <li><a class="dropdown-item" href="<?= site_url('/logout') ?>">Logout</a></li>
        </ul>
      </li>
    </ul>
  </nav>
  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
          <div class="nav">
            <a class="nav-link" href="<?= site_url('/'); ?>">
              <div class="sb-nav-link-icon"><i class="fa-solid fa-left-long"></i></div>
              Halaman Pengguna
            </a>
            <a class="nav-link" href="<?= site_url('/admin/dashboard'); ?>">
              <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
              Dashboard
            </a>

            <div class="sb-sidenav-menu-heading">Interface</div>
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
              <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
              Pages
              <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
              <nav class="sb-sidenav-menu-nested nav">
                <a class="nav-link" href="<?= site_url('/'); ?>">Home</a>
                <a class="nav-link" href="<?= site_url('/login'); ?>">Login</a>
                <a class="nav-link" href="<?= site_url('/register'); ?>">Register</a>
                <a class="nav-link" href="<?= site_url('/question/mengapa-bumi-itu-bulat'); ?>">View Question</a>
                <a class="nav-link" href="<?= site_url('/ask'); ?>">Ask Question</a>
                <a class="nav-link" href="<?= site_url('/questions/edit/14'); ?>">Edit Question</a>
                <a class="nav-link" href="<?= site_url('/profile'); ?>">View Profile</a>
                <a class="nav-link" href="<?= site_url('/profile/edit'); ?>">Edit Profile</a>
                <a class="nav-link" href="<?= site_url('/answer/edit/13'); ?>">Edit Answer</a>
              </nav>
            </div>

            <div class="sb-sidenav-menu-heading">Tables</div>
            <a class="nav-link" href="<?= site_url('/admin/tables/users'); ?>">
              <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div>
              Users
            </a>
            <a class="nav-link" href="<?= site_url('/admin/tables/questions'); ?>">
              <div class="sb-nav-link-icon"><i class="fa-solid fa-circle-question"></i></div>
              Questions
            </a>
            <a class="nav-link" href="/admin/tables/answers">
              <div class="sb-nav-link-icon"><i class="fa-solid fa-paper-plane"></i></div>
              Answers
            </a>
            <a class="nav-link" href="/admin/tables/answer-ratings">
              <div class="sb-nav-link-icon"><i class="fa-solid fa-star"></i></div>
              Answer Ratings
            </a>
            <a class="nav-link" href="/admin/tables/answer-comments">
              <div class="sb-nav-link-icon"><i class="fa-solid fa-comment"></i></div>
              Answer Comments
            </a>
            <a class="nav-link" href="/admin/tables/topics">
              <div class="sb-nav-link-icon"><i class="fa-solid fa-newspaper"></i></div>
              Topics
            </a>
            <a class="nav-link" href="/admin/tables/question-topics">
              <div class="sb-nav-link-icon"><i class="fa-solid fa-link"></i></div>
              Question Topics
            </a>
          </div>
        </div>
        <div class="sb-sidenav-footer">
          <div class="small">Logged in as:</div>
          <?= session()->get('nama_lengkap'); ?>
        </div>
      </nav>
    </div>
    <div id="layoutSidenav_content">

      <?= $this->renderSection('content'); ?>

      <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
          <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; Argumentum 2025</div>
            <div>
              <a href="#">Privacy Policy</a>
              &middot;
              <a href="#">Terms &amp; Conditions</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="/assets/js/admin-scripts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
  <script src="/admin/demo/chart-area-demo.js"></script>
  <script src="/admin/demo/chart-bar-demo.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
  <script src="/assets/js/datatables-simple-demo.js"></script>
</body>

</html>