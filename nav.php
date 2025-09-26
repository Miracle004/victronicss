<div class="page">
  <div class="pageHeader">
  <img class="logo" src="images/images (1).jpeg" alt="Tasks" class="title-image" />
    <div class="userPanel">
      <span class="username"><?php echo htmlspecialchars($user_details['empName']); ?></span>
      <form action="logout.php" method="POST" style="display:inline;">
        <button style="margin-left: 30px" type="submit" class="logoutButton">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
              <path d="M16 13v-2H7V9l-5 3 5 3v-2h9zM13 3v2h4v14h-4v2h6V3h-6z"/>
          </svg>
        </button>
      </form>
      <img style="width:40px; height:40px;" src="https://gravatar.com/avatar/27205e5c51cb03f862138b22bcb5dc20f94a342e744ff6df1b8dc8af3c865109" />
    </div>
  </div>

  <div class="main">
    <!-- Navigation Menu -->
    <div class="nav">
      <div class="searchbox">
        <div style="display: flex; align-items: center; gap: 8px;">
          <i class="fa fa-search"></i>
          <input type="search" placeholder="Search" style="padding: 5px;" />
        </div>
      </div>
      <div class="menu">
        <div class="title">Navigation</div>
        <ul>
          <li>
            <a href="home.php" style="text-decoration: none; color: inherit;">
              <i class="fa fa-home"></i> Home
            </a>
          </li>
          <li>
            <a href="task.php" style="text-decoration: none; color: inherit;">
              <i class="fa fa-tasks"></i> Tasks
            </a>
          </li>
          <li class="active">
            <a href="profile.php" style="text-decoration: none; color: inherit;">
              <i class="fa fa-user"></i> Profile
            </a>
          </li>
        </ul>
      </div>
    </div>

    <style>
    .logo{
    width: 130px;
    height: auto;
    margin-top: 5px;
  }
</style>